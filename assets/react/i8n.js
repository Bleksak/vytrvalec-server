import i18next from 'i18next';
import yaml from 'js-yaml';
import I18NextHttpBackend from 'i18next-http-backend';
import { initReactI18next } from 'react-i18next';

i18next
    .use(I18NextHttpBackend)
    .use(initReactI18next)
    .init({
        lng: 'en',
        fallbackLng: 'en',
        backend: {
            loadPath: '/build/translations/messages.{{lng}}.yaml',
            parse: function(data) { return yaml.load(data) },
        },
        interpolation: {
            escapeValue: false
        }
    })