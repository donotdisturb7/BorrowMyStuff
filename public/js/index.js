const pageData = {
    div: document.querySelector('.--scroll'),
    next: document.querySelector('.next'),
    prev: document.querySelector('.prev'),
    submit: document.querySelector('.submit'),

    value: 0,
    max: 1,

    slide: function (direction) {

        if (direction == 'next' && this.value < this.max || direction == 'prev' && this.value >= 1) {
            this.value += direction == 'next' ? 1 : -1;
            this.div.style.transform = `translateX(-${(this.value) * 100}%)`;
        }

        this.div.setAttribute('data-page', this.value);
        this.prev.classList.toggle('hidden', pageData.value === 0);
        this.next.classList.toggle('hidden', this.value == 1);
        this.submit.classList.toggle('hidden', this.value != 1);

    }
};


pageData.next.addEventListener('click', () => pageData.slide('next'));

pageData.prev.addEventListener('click', () => pageData.slide('prev'));


