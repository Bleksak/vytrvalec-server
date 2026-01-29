import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
  connect() {
    this.element.addEventListener('chartjs:pre-connect', this._onPreConnect);
    this.element.addEventListener('chartjs:connect', this._onConnect);
  }

  disconnect() {
    if (this.chart) {
      this.chart.destroy();
    }

    this.element.removeEventListener('chartjs:pre-connect', this._onPreConnect);
    this.element.removeEventListener('chartjs:connect', this._onConnect);
  }

  _onConnect(event) {
    this.chart = event.detail.chart;
  }

  _onPreConnect(event) {
    if (this.chart) {
      this.chart.destroy();
    }

    event.detail.options.plugins.tooltip = {
      callbacks:
      {
        label: (item) => item.dataset.label + ': ' + item.formattedValue + ' km'
      }
    };
  }
}
