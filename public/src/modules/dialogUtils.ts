function bindMouseMove(binderID: string, moveDivID: string): void {
    const binder = document.getElementById(binderID);
    const mover = document.getElementById(moveDivID);

    binder.addEventListener('mousedown', (e: MouseEvent) => {
        if (e.which !== 1) { return; }

        const self = e.target as HTMLElement;
        self.style.position = 'relative';

        document.onmousemove = e => {
            e.preventDefault();
            mover.style.left = e.pageX - (self.clientWidth / 2) + 'px';
            mover.style.top = e.pageY - (self.clientHeight / 2) + 'px';
        };
    });

    binder.addEventListener('mouseup', () => document.onmousemove = null);

    binder.addEventListener('dragstart', () => false);
}

function centerDialog(id: string): void {
    const elem = document.getElementById(id);
    elem.style.left = (window.innerWidth/2) - (elem.clientWidth/2) + 'px';
    elem.style.top = (window.innerHeight/2) - (elem.clientHeight/2) + 'px';
}

export { bindMouseMove, centerDialog }
