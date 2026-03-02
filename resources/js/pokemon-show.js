document.addEventListener('DOMContentLoaded', () => {
  const dataEl = document.getElementById('pokemonVariantData');
  const img = document.getElementById('pokemonImage');
  const typesEl = document.getElementById('pokemonTypes');

  const shinyToggle = document.getElementById('shinyToggle');
  const btnContainer = document.getElementById('variantButtons');

  const selectedFormInput = document.getElementById('selectedFormInput');
  if (!dataEl || !img || !btnContainer) return;

  let variants = {};
  try {
    variants = JSON.parse(dataEl.dataset.variants || '{}');
  } catch (e) {
    console.error('Impossible de parser variants', e);
    variants = {};
  }

  // ====== Stats DOM refs
  const statKeys = ['hp','attack','defense','special_attack','special_defense','speed'];
  const valEls = Object.fromEntries(statKeys.map(k => [k, document.getElementById(`val-${k}`)]));
  const barEls = Object.fromEntries(statKeys.map(k => [k, document.getElementById(`bar-${k}`)]));

  // ⚙️ Max stat (tu peux changer ici)
  const STAT_MAX = 255;

  let currentVariantKey = 'normal';
  let shinyOn = false;

  function pct(value) {
    const v = Math.max(0, Number(value) || 0);
    return Math.max(0, Math.min(100, (v / STAT_MAX) * 100));
  }

  function setTypes(type1, type2) {
    if (!typesEl) return;
    const badges = [];
    if (type1) badges.push(`<span class="type-badge">${String(type1).charAt(0).toUpperCase() + String(type1).slice(1)}</span>`);
    if (type2) badges.push(`<span class="type-badge">${String(type2).charAt(0).toUpperCase() + String(type2).slice(1)}</span>`);
    typesEl.innerHTML = badges.join('');
  }

  function setStats(stats) {
    statKeys.forEach(k => {
      const v = stats?.[k] ?? 0;

      if (valEls[k]) valEls[k].textContent = v;

      if (barEls[k]) {
        barEls[k].style.width = `${pct(v)}%`;
      }
    });
  }

  function setImage(variant) {
    const src = shinyOn && variant.image_shiny ? variant.image_shiny : variant.image_default;
    if (!src) return;

    img.classList.add('switching');
    setTimeout(() => {
      img.src = src;
      img.onload = () => img.classList.remove('switching');
      setTimeout(() => img.classList.remove('switching'), 250);
    }, 60);
  }

  function applyVariant(key) {
    const variant = variants[key];
    if (!variant) return;

    currentVariantKey = key;

    if (selectedFormInput) {
        selectedFormInput.value = key;
    }

    btnContainer.querySelectorAll('.variant-btn')
        .forEach(b => b.classList.remove('active'));

    const btn = btnContainer.querySelector(`.variant-btn[data-variant="${CSS.escape(key)}"]`);
    if (btn) btn.classList.add('active');

    shinyToggle?.classList.toggle('active', shinyOn);

    setImage(variant);
    setTypes(variant.type1, variant.type2);
    setStats(variant.stats);
  }

  // Click variant
  btnContainer.addEventListener('click', (e) => {
    const btn = e.target.closest('.variant-btn');
    if (!btn) return;

    // shiny toggle
    if (btn.id === 'shinyToggle') {
      shinyOn = !shinyOn;
      shinyToggle.classList.toggle('active', shinyOn);
      applyVariant(currentVariantKey);
      return;
    }

    const key = btn.dataset.variant;
    if (!key) return;

    applyVariant(key);
  });

  // init
  applyVariant('normal');
});
