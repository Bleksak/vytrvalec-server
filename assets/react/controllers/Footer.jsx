import {useTranslation} from "react-i18next";
import React from "react";

export default function Footer() {
    const [t, _] = useTranslation();

    return <footer className="text-center">
        <div className="row">
            <div className="col">
                {t('ZCU')}<br/>
                {t('KTS')}<br/>
                <br/>
                <a href="https://www.facebook.com/KatedraTelesneVychovyASportuZcuVPlzni">
                    <i className="fa-brands fa-facebook fa-2xl icon"></i>
                    Facebook
                </a>
                <a href="https://www.instagram.com/kts.zcu/">
                    <i className="fa-brands fa-instagram-square fa-2xl icon"></i>
                    Instagram
                </a>
            </div>
        </div>
    </footer>
}