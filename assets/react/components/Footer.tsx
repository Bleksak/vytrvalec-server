import { useTranslation } from "react-i18next";
import React from "react";
import {BsFacebook, BsInstagram} from "react-icons/bs";

const Footer = () => {
    const [t, _] = useTranslation();

    return <footer>
        <p>{t('ZCU')}</p>
        <p>{t('KTS')}</p>

        <div>
            <a href="https://www.facebook.com/KatedraTelesneVychovyASportuZcuVPlzni">
                <BsFacebook size={40} className='px-2'/>
                Facebook
            </a>
            <a href="https://www.instagram.com/kts.zcu/">
                <BsInstagram size={40} className='px-2'/>
                Instagram
            </a>
        </div>
    </footer>
}

export default Footer;