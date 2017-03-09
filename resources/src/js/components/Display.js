class Display {

    constructor(selector) {

        this.element = selector;
        this.inputElement = document.querySelector(`${selector} input[type=hidden]`);
        this.hideWhenOn('#entryId--wrapper');
        this.hideWhenOff('#customUrl--wrapper');
        this.showClass = 'c-autolink';
        this.hideClass = 'c-autolink--hidden';

        this.init();

    }

    value() {
        return Boolean(this.inputElement.value);
    }

    setShowClass(className) {
        this.showClass = className;
        return this;
    }

    setHideClass(className) {
        this.hideClass = className;
        return this;
    }

    hideWhenOn(selector) {
        this.hideWhenOn = document.querySelectorAll(selector);
        return this;
    }

    hideWhenOff(selector) {
        this.hideWhenOff = document.querySelectorAll(selector);
        return this;
    }

    init() {

        if (this.value()) {
            for (let on of this.hideWhenOn) {
                if (!on.classList.contains(this.hideClass)) {
                    on.classList.add(this.hideClass);
                }
            }
            for (let off of this.hideWhenOff) {
                if (off.classList.contains(this.hideClass)) {
                    off.classList.remove(this.hideClass);
                }
            }
        } else {
            for (let on of this.hideWhenOn) {
                if (on.classList.contains(this.hideClass)) {
                    on.classList.remove(this.hideClass);
                }
            }
            for (let off of this.hideWhenOff) {
                if (!off.classList.contains(this.hideClass)) {
                    off.classList.add(this.hideClass);
                }
            }
        }

        window.Garnish.$doc
            .on('change', this.element, () => {
                this.toggleElements();
            });
    }

    toggleElements() {
        for (let on of this.hideWhenOn) {
            on.classList.toggle(this.hideClass);
        }
        for (let off of this.hideWhenOff) {
            off.classList.toggle(this.hideClass);
        }
    }
}

export default Display;