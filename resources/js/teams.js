document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('teamForm');
  const counter = document.getElementById('counter');
  const selectedSlots = document.getElementById('selectedSlots');
  const hiddenInputs = document.getElementById('hiddenInputs');

  if (!form || !counter || !selectedSlots || !hiddenInputs) return;

  const MAX = 6;
  let selected = [];

  // Préselection (edit)
  try {
    const pre = form.dataset.preselected ? JSON.parse(form.dataset.preselected) : [];
    if (Array.isArray(pre)) {
      selected = pre.map(n => parseInt(n, 10)).filter(Boolean).slice(0, MAX);
    }
  } catch (e) {}

  function render() {
    counter.textContent = `${selected.length}/${MAX}`;

    // slots preview
    selectedSlots.querySelectorAll('.selected-slot').forEach((slotEl) => {
      const slot = parseInt(slotEl.dataset.slot, 10);
      const pid = selected[slot - 1];

      slotEl.innerHTML = '';
      if (!pid) {
        const empty = document.createElement('div');
        empty.className = 'selected-empty';
        empty.textContent = `Slot ${slot}`;
        slotEl.appendChild(empty);
        return;
      }

      const card = document.querySelector(`.poke-card[data-id="${pid}"]`);
      const name = card?.dataset.name || `#${pid}`;
      const img = card?.dataset.img || '';

      const imgEl = document.createElement('img');
      imgEl.src = img;
      imgEl.alt = name;
      imgEl.loading = 'lazy';
      imgEl.onerror = () => (imgEl.style.display = 'none');

      const nameEl = document.createElement('div');
      nameEl.className = 'selected-name';
      nameEl.textContent = name;

      slotEl.appendChild(imgEl);
      slotEl.appendChild(nameEl);
    });

    // hidden inputs (ordre conservé)
    hiddenInputs.innerHTML = '';
    selected.forEach((pid) => {
      const input = document.createElement('input');
      input.type = 'hidden';
      input.name = 'pokemons[]';
      input.value = String(pid);
      hiddenInputs.appendChild(input);
    });

    // cartes état
    document.querySelectorAll('.poke-card').forEach((btn) => {
      const pid = parseInt(btn.dataset.id, 10);
      const idx = selected.indexOf(pid);

      const badge = btn.querySelector('.pick-badge');
      if (idx !== -1) {
        btn.classList.add('active');
        if (badge) badge.textContent = String(idx + 1);
      } else {
        btn.classList.remove('active');
        if (badge) badge.textContent = '+';
      }
    });
  }

  function togglePokemon(pid) {
    const idx = selected.indexOf(pid);
    if (idx !== -1) {
      selected.splice(idx, 1);
      return;
    }
    if (selected.length >= MAX) {
      alert('Max 6 Pokémon par team.');
      return;
    }
    selected.push(pid);
  }

  document.addEventListener('click', (e) => {
    const btn = e.target.closest('.poke-card');
    if (!btn) return;
    e.preventDefault();

    const pid = parseInt(btn.dataset.id, 10);
    if (!pid) return;

    togglePokemon(pid);
    render();
  });

  render();
});
