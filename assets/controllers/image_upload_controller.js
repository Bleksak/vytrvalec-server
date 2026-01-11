import { Controller } from '@hotwired/stimulus'

export default class extends Controller {
    static targets = ['image', 'input', 'dropzoneText']
    static values = { disabled: Boolean }

    constructor(context) {
        super(context);

        this.handleDrop = this.handleDrop.bind(this);
        this.chooseFile = this.chooseFile.bind(this);
        this.displayImage = this.displayImage.bind(this);
        this.updateImagePreview = this.updateImagePreview.bind(this);
    }

    connect() {
        this.element.addEventListener('click', (e) => this.chooseFile(e, this.disabledValue));
        this.element.addEventListener('dragenter', (e) => e.preventDefault());
        this.element.addEventListener('dragover', (e) => e.preventDefault());
        this.element.addEventListener('dragleave', (e) => e.preventDefault());
        this.element.addEventListener('drop', this.handleDrop);
    }

    /**
     * @param {DragEvent} event
     */
    handleDrop(event) {
        const dt = event.dataTransfer;
        if (dt === null) {
            return;
        }

        const files = dt.files;
        if (files.length === 0) {
            return;
        }

        const file = files[0];

        let fileList = new DataTransfer();
        fileList.items.add(file);
        uploadedFiles = fileList.files;

        this.updateImagePreview(file);
    }

    /**
     * @param {string} url
     */
    displayImage(url) {
        this.imageTarget.src = url;
        this.imageTarget.style.display = 'block';
        this.dropzoneTextTarget.style.display = 'none';
    }

    /**
     * @param {File} file
     */
    updateImagePreview(file) {
        let reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onloadend = () => {
            this.displayImage(reader.result);
        };
    };

    chooseFile(_, disabled) {
        if (!disabled) {
            this.inputTarget.click()
        }
    }
}
