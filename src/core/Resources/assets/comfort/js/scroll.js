function executeFunc(func) {
    if (typeof func !== 'function') {
        return false;
    }

    func();
    return true;
}


function scrollButton() {
  const scrollContainer = document.querySelector('.scroll-container');
  const scrollButtons = [
    { id: '#scroll-left', amount: -213 },
    { id: '#scroll-right', amount: 213 },
    { id: '#scroll-leftBig', amount: -270 },
    { id: '#scroll-rightBig', amount: 270 },
  ];

  scrollButtons.forEach((button) => {
    const buttonElement = document.querySelector(button.id);
    buttonElement?.addEventListener('click', () => {
      scrollContainer?.scrollTo({
        left: scrollContainer?.scrollLeft + button.amount,
        behavior: 'smooth',
      });
    });
  });

  (function(){
    let speed = 2; // Скорость скролла.
    
    let scroll = document.querySelector('.scroll-container');
    
    let left = 0; // отпустили мышку - сохраняем положение скролла
    let drag = false;
    let coorX = 0; // нажали мышку - сохраняем координаты.
    
    scroll?.addEventListener('mousedown', function(e) {
      drag = true;
      coorX = e.pageX - this.offsetLeft;
    });
    document.addEventListener('mouseup', function() {
      drag = false;
      left = scroll?.scrollLeft;
    });
    scroll?.addEventListener('mousemove', function(e) {
      if (drag) {
        this.scrollLeft = left - (e.pageX - this.offsetLeft - coorX)*speed;
      }
    });
    
  })();
}

function scrollButtonProd() {
  const scrollContainer = document.querySelector('.scroll-container-prod');
  const scrollButtons = [
    { id: '#scroll-leftProd', amount: -316 },
    { id: '#scroll-rightProd', amount: 316 },
  ];

  scrollButtons.forEach((button) => {
    const buttonElement = document.querySelector(button.id);
    buttonElement?.addEventListener('click', () => {
      scrollContainer?.scrollTo({
        left: scrollContainer?.scrollLeft + button.amount,
        behavior: 'smooth',
      });
    });
  });
}
  

executeFunc(scrollButton);
executeFunc(scrollButtonProd);