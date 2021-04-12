document.addEventListener('DOMContentLoaded', () => {
  const lostfoundForm = document.getElementById('lostfound');

  console.log(lfzs_key)

  lostfoundForm.addEventListener('submit', () => {
    const zsInput = document.createElement('input');
    zsInput.setAttribute('name', 'lostfound_zerospam_key');
    zsInput.value = lfzs_key;
    zsInput.hidden = true;
    lostfoundForm.appendChild(zsInput);
  });
})