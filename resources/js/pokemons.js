document.addEventListener('DOMContentLoaded', () => {
  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
  if (!csrfToken) {
    console.error('CSRF token introuvable.');
    return;
  }

  // =========================
  // Helpers
  // =========================
  async function postJson(url) {
    const response = await fetch(url, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': csrfToken,
        'Accept': 'application/json',
      },
    });

    if (!response.ok) {
      const raw = await response.text().catch(() => '');
      throw new Error(`HTTP ${response.status} ${raw}`);
    }
    return response.json().catch(() => ({}));
  }

  async function postJsonBody(url, bodyObj) {
    const response = await fetch(url, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': csrfToken,
        'Accept': 'application/json',
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(bodyObj || {}),
    });

    if (!response.ok) {
      const raw = await response.text().catch(() => '');
      throw new Error(`HTTP ${response.status} ${raw}`);
    }
    return response.json().catch(() => ({}));
  }

  function setCardUnlocked(card, unlocked) {
    const sprite = card.querySelector('.sprite');
    const btn = card.querySelector('.unlock-btn');
    const text = btn?.querySelector('.unlock-text');

    card.dataset.unlocked = unlocked ? '1' : '0';

    if (unlocked) {
      sprite?.classList.remove('locked');
      if (btn) {
        btn.disabled = true;
        btn.classList.add('unlocked');
      }
      if (text) text.textContent = 'Unblocked';
    } else {
      sprite?.classList.add('locked');
      if (btn) {
        btn.disabled = false;
        btn.classList.remove('unlocked');
      }
      if (text) text.textContent = 'Unblock';
    }
  }

  function setCardShiny(card, shinyOn) {
    const img = card.querySelector('img.poke-img');
    const shinyBtn = card.querySelector('.shiny-btn');
    if (!img) return;

    const normal = img.dataset.normal;
    const shiny = img.dataset.shiny;

    // pas de shiny dispo → on ignore
    if (!shiny) return;

    if (shinyOn) {
      img.src = shiny;
      shinyBtn?.classList.add('active');
    } else {
      img.src = normal;
      shinyBtn?.classList.remove('active');
    }
  }

  // =========================
  // 1) UNLOCK 1 (AJAX)
  // =========================
  document.addEventListener('click', async (e) => {
    const btn = e.target.closest('.unlock-btn');
    if (!btn) return;

    const url = btn.dataset.url;
    if (!url) return;

    e.preventDefault();
    if (btn.disabled) return;

    const card = btn.closest('.card');
    const text = btn.querySelector('.unlock-text');
    if (!card) return;

    btn.disabled = true;
    if (text) text.textContent = '.';

    try {
      const data = await postJson(url);

      if (data.success) {
        setCardUnlocked(card, true);
      } else {
        btn.disabled = false;
        if (text) text.textContent = 'Unblock';
        alert("Unable to unlock (unexpected response).");
      }
    } catch (err) {
      btn.disabled = false;
      if (text) text.textContent = 'Unblock';
      console.error(err);
      alert("An error occurred during the unlocking process.");
    }
  });

  // =========================
  // 2) SHINY toggle individuel
  // =========================
  document.addEventListener('click', (e) => {
    const btn = e.target.closest('.shiny-btn');
    if (!btn) return;

    if (btn.disabled) return;
    if (btn.dataset.hasShiny !== '1') return;

    const card = btn.closest('.card');
    const img = card?.querySelector('img.poke-img');
    if (!img) return;

    const normal = img.dataset.normal;
    const shiny = img.dataset.shiny;
    if (!normal || !shiny) return;

    const isActive = btn.classList.contains('active');
    if (isActive) {
      img.src = normal;
      btn.classList.remove('active');
    } else {
      img.src = shiny;
      btn.classList.add('active');
    }
  });

  // =========================
  // 3) GO TO PAGE (Enter + OK)
  // =========================
  const gotoInput = document.getElementById('gotoPage');
  const gotoBtn = document.getElementById('gotoBtn');

  function goToPage() {
    if (!gotoInput) return;
    const lastPage = parseInt(gotoInput.getAttribute('max') || '1', 10);
    const value = parseInt(gotoInput.value || '1', 10);
    const page = Math.max(1, Math.min(value, lastPage));

    const url = new URL(window.location.href);
    url.searchParams.set('page', String(page));
    window.location.href = url.toString();
  }

  gotoBtn?.addEventListener('click', goToPage);
  gotoInput?.addEventListener('keydown', (event) => {
    if (event.key === 'Enter') {
      event.preventDefault();
      goToPage();
    }
  });

  // ===================
  // 4) Tout en shiny
  // ===================
  const toggleAllShinyBtn = document.getElementById('toggleAllShinyBtn');
  let allShinyOn = false;

  toggleAllShinyBtn?.addEventListener('click', () => {
    allShinyOn = !allShinyOn;

    document.querySelectorAll('.card').forEach(card => {
      setCardShiny(card, allShinyOn);
    });

    toggleAllShinyBtn.classList.toggle('active', allShinyOn);
    toggleAllShinyBtn.textContent = allShinyOn ? '✨ All normal' : '✨ All shiny';
  });

  // =========================
  // 5) Tout débloquer / Tout bloquer
  // =========================
  const unlockAllBtn = document.getElementById('unlockAllBtn');
  const lockAllBtn = document.getElementById('lockAllBtn');

  unlockAllBtn?.addEventListener('click', async () => {
    const url = unlockAllBtn.dataset.url;
    if (!url) return;

    unlockAllBtn.disabled = true;
    unlockAllBtn.textContent = "...";

    try {
      const response = await fetch(url, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': csrfToken,
          'Accept': 'application/json',
        },
      });

      if (!response.ok) {
        throw new Error("Erreur HTTP " + response.status);
      }

      const data = await response.json();

      if (data.success) {
        document.querySelectorAll('.card').forEach(card => {
          setCardUnlocked(card, true);
        });
      } else {
        alert("Invalid server response.");
      }

    } catch (err) {
      console.error(err);
      alert("Error during 'Unlock all'.");
    } finally {
      unlockAllBtn.disabled = false;
      unlockAllBtn.textContent = "🔓 Unlock everything";
    }
  });

  lockAllBtn?.addEventListener('click', async () => {
    const url = lockAllBtn.dataset.url;
    if (!url) return;

    lockAllBtn.disabled = true;

    try {
      const data = await postJson(url);
      if (data.success) {
        document.querySelectorAll('.card').forEach(card => setCardUnlocked(card, false));
      }
    } catch (err) {
      console.error(err);
      alert("Error during 'Block all'.");
    } finally {
      lockAllBtn.disabled = false;
    }
  });

  // =========================
  // 6) Débloquer cette page
  // =========================
  const unlockPageBtn = document.getElementById('unlockPageBtn');
  unlockPageBtn?.addEventListener('click', async () => {
    const url = unlockPageBtn.dataset.url;
    if (!url) return;

    const ids = Array.from(document.querySelectorAll('.card'))
      .filter(card => card.dataset.unlocked !== '1')
      .map(card => parseInt(card.dataset.pokemonId, 10))
      .filter(n => Number.isFinite(n));

    if (ids.length === 0) {
      alert("All the Pokémon on this page are already unlocked.");
      return;
    }

    unlockPageBtn.disabled = true;
    unlockPageBtn.textContent = '...';

    try {
      const data = await postJsonBody(url, { ids });

      if (data.success) {
        document.querySelectorAll('.card').forEach(card => setCardUnlocked(card, true));
      } else {
        alert("Invalid server response.");
      }
    } catch (err) {
      console.error(err);
      alert("Error during 'Unlock this page'.");
    } finally {
      unlockPageBtn.disabled = false;
      unlockPageBtn.textContent = '🔓 Unlock this page';
    }
  });

  // =========================
  // 7) Débloquer une génération (selon select)
  // =========================
  const unlockGenBtn = document.getElementById('unlockGenBtn');
  const generationSelect = document.getElementById('generation');

  unlockGenBtn?.addEventListener('click', async () => {
    const url = unlockGenBtn.dataset.url;
    if (!url) return;

    const gen = parseInt(generationSelect?.value || '', 10);
    if (!Number.isFinite(gen)) {
      alert("First, choose a generation in the filter (Gen 1, Gen 2...).");
      return;
    }

    unlockGenBtn.disabled = true;
    unlockGenBtn.textContent = '...';

    try {
      const data = await postJsonBody(url, { generation: gen });

      if (data.success) {
        document.querySelectorAll('.card').forEach(card => setCardUnlocked(card, true));
        alert(`Génération ${gen} unlocked !`);
      } else {
        alert("Invalid server response.");
      }
    } catch (err) {
      console.error(err);
      alert("Error during 'Unlock generation'.");
    } finally {
      unlockGenBtn.disabled = false;
      unlockGenBtn.textContent = '🔓 Unlock Gen';
    }
  });
});