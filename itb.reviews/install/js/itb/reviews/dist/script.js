class Popup {
    constructor(element) {
        this.popup = element;
        this.container = this.popup.querySelector(".js-popup-container");
        this.closeBtn = this.popup.querySelector(".js-popup-close");
        this.name = this.popup.getAttribute("data-popup");
        this.buttons = document.querySelectorAll(`[data-popup="${this.name}"]:not(.js-popup)`);
        this.html = document.documentElement;
        this.body = document.body;
        this.init();
    }

    init() {
        this.closeBtn.addEventListener("click", () => this.close());
        this.buttons.forEach(button => {
            button.addEventListener("click", () => this.open());
        });
        this.popup.addEventListener("click", (e) => this.eventPopupClick(e));
    }

    open() {
        this.popup.classList.add("mod-show");
        this.html.classList.add("mod-popup-is-open");
        this.body.classList.add("mod-popup-is-open");
    }

    close() {
        this.popup.classList.remove("mod-show");
        this.html.classList.remove("mod-popup-is-open");
        this.body.classList.remove("mod-popup-is-open");
    }

    //eventPopupClick(e) {
    //    if (e.target === this.popup) {
    //        this.close();
    //    }
    //}
}

class PopupManager {
    constructor() {
        this.popups = {};
        this.initPopups();
        
        window.addEventListener("keydown", (e) => {
            if (e.key === "Escape") {
                Object.values(this.popups).forEach(popupInstance => popupInstance.close());
            }
        });
        document.addEventListener('popup-added', () => this.reinitialize());
    }

    close(name) {
        if(this.popups[name]){
            this.popups[name].close();
        }
    }

    initPopups() {
        const popups = document.querySelectorAll(".js-popup");
        popups.forEach(popup => {
            this.popups[popup.getAttribute("data-popup")] = new Popup(popup);
        });
    }

    reinitialize() {
        this.popups = {};
        this.initPopups();
    }
}

document.addEventListener("DOMContentLoaded", () => {
    window.popups = new PopupManager();
});
