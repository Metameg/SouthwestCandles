import $ from 'jquery'

(function () {
    const frageranceListItems = document.querySelectorAll('.radio-selector');
    frageranceListItems.forEach(item => {
        item.addEventListener('click', function() {
            const radioButton = this.querySelector('.custom-radio');
            if (radioButton) {
                radioButton.checked = true;
            }
        });
    });
})();