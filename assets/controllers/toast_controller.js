import { Controller } from '@hotwired/stimulus';
import { getComponent } from '@symfony/ux-live-component';

export default class extends Controller {
  connect() {
    this.interval = setInterval(() => {
      this.closeToast();
    }, 5000);

    this.element.onclick = () => { this.closeToast(); };
    getComponent(this.element.parentElement.parentElement).then((component) => {
      this.component = component;
    });
  }

  closeToast() {
    clearInterval(this.interval);

    this.element.remove();
    this.component.emit('toast-remove', { id: this.element.id });
  }
}
