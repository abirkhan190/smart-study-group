// Live search for groups (name or subject)
const search = document.getElementById('search');
if (search) {
  search.addEventListener('input', () => {
    const q = search.value.toLowerCase().trim();
    document.querySelectorAll('.group[data-text]').forEach(card => {
      card.style.display = card.dataset.text.includes(q) ? '' : 'none';
    });
  });
}
