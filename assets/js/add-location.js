// Toggle Form Visibility
document.querySelector('.toggle-btn').addEventListener('click', () => {
    const form = document.getElementById('addForm');
    form.style.display = form.style.display === 'none' || form.style.display === '' ? 'block' : 'none';
  });
  