import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
  connect() {
    this.element.addEventListener('dialog:close', () => {
      this.element.close();
    });
  }
}
