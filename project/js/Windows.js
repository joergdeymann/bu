export class Windows {
    content = "";
    style = true;
    sleepTimer = 2000;

    styles = {
        width: "33vw",
        height: "33vw",
        background: "rgba(255,255,255,0.90)",
        border: "1px solid grey",
        boxShadow: "2px 2px 4px rgba(0,0,0,0.3)",
        borderRadius: "10px",
    };

    containerStyles = {
        position: "fixed",
        top: 0,
        left: 0,
        right: 0,
        bottom: 0,
        display: "flex",
        justifyContent: "center",
        alignItems: "center",
        backgroundColor: "rgba(0,0,0,0.2)",
        zIndex: 100,
    };

    constructor() {
        console.log("Erstellt");
        this.prepare();
        this.create();
    }

    hide() {
        this.container.style.display = "none";
    }

    show() {
        this.container.style.display = "";
    }

    setSize(x, y) {
        this.styles.width = x;
        this.styles.height = y;
    }

    setColor(color) {
        this.styles.color = color;
    }

    setBackground(background) {
        this.styles.background = background;
    }

    setContainer(background) {
        this.containerStyles.backgroundColor = background;
    }

    setContent(content) {
        this.win.innerHTML = content;
    }

    addStyle(styles) {
        if (!this.win || !(this.win instanceof HTMLElement)) {
            console.error("Das Ziel-Element ist ungültig oder nicht definiert.");
            return;
        }
        Object.assign(this.win.style, styles);
    }

    addStyles(element, styles) {
        Object.entries(styles).forEach(([key, value]) => {
            const cssKey = key.replace(/([A-Z])/g, "-$1").toLowerCase();
            element.style[cssKey] = value;
        });
    }

    prepare() {
        this.container = document.createElement("div");
        this.addStyles(this.container, this.containerStyles);

        this.win = document.createElement("div");
        this.addStyles(this.win, this.styles);
        this.win.innerHTML = this.content;
        this.container.appendChild(this.win);
    }

    create() {
        document.body.appendChild(this.container);
    }

    async start(choice) {
        this.addAnimation(choice) ;
        await new Promise(e => setTimeout(e,5));
        this.addStyle(this.animation);
        await new Promise((e) => setTimeout(e, this.sleepTimer));
    }

    addAnimation(choice) {
        switch (choice) {
            case "BottomToCenter":
                this.addStyle({
                    // opacity: "0",
                    transform: "translate(0, 200vh)",
                    transition: "0ms ease-in-out",
                });

                this.animation = {
                    // opacity: "1",
                    transform: "translate(0, 0)",
                    transition: "1000ms ease-in-out",
                };
                this.sleepTimer = 1000;
                
                return;

            case "opacity":
                this.animation = {
                    opacity: "0",
                    transition: "500ms ease-in-out 500ms",
                };
                this.sleepTimer = 1000;
                return;
        }
    }
}
