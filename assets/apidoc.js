import hljs from "highlight.js";

import './styles/hightlightjs-dark.css';
import './styles/style.css';

function toggleMenu(e) {
    e.preventDefault();
    document.querySelector('html').classList.toggle('menu-opened');
}

for(let element of document.querySelectorAll('#button-menu-mobile, .left-menu .mobile-menu-closer')) {
    element.onclick = toggleMenu;
}

hljs.highlightAll();