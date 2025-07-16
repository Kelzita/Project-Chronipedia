
  const inputImagem = document.getElementById('imagem');
  const previewImg = document.getElementById('preview-img');
  const previewContainer = document.getElementById('preview-container');

  previewContainer.addEventListener('click', () => {
    inputImagem.click();
  });

  inputImagem.addEventListener('change', function () {
    const file = this.files[0];
    if (file) {
      const reader = new FileReader();
      reader.onload = function (e) {
        previewImg.src = e.target.result;
        previewImg.style.transform = 'scale(1.05)';
        setTimeout(() => {
          previewImg.style.transform = 'scale(1)';
        }, 300);
      }
      reader.readAsDataURL(file);
    }
  });
