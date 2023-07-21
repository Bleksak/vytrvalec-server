import { useTranslation } from "react-i18next";
import React from "react";

const Footer = () => {
    const [t, _] = useTranslation();

    return <footer>
        <p>{t('ZCU')}</p>
        <p>{t('KTS')}</p>

        <div>
            <a href="https://www.facebook.com/KatedraTelesneVychovyASportuZcuVPlzni">
                <i className="fa-brands fa-facebook fa-2xl icon px-2"></i>
                Facebook
            </a>
            <a href="https://www.instagram.com/kts.zcu/">
                <i className="fa-brands fa-instagram-square fa-2xl icon px-2"></i>
                Instagram
            </a>
        </div>
    </footer>
}

export default Footer;