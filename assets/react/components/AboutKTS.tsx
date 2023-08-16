import React from "react";
import { Image } from "react-bootstrap";
import { useTranslation } from "react-i18next";
import { FaFacebookSquare, FaInstagramSquare } from 'react-icons/fa'

const AboutKTS = () => {
    const [t, _] = useTranslation();

    return (

        <div className="about-container">
            <div className="zcu-logo">
                <div style={{ width: '50%', }}>
                    <Image src={require('/assets/images/zcu-logo.png')} />
                </div>
            </div>

            <div>
                <div className="kts-header">
                    <p style={{ color: '#00aaffff' }}><strong>KATEDRA</strong></p>
                    <p style={{ color: '#999999ff' }}><strong>TĚLESNÉ VÝCHOVY A SPORTU</strong></p>
                </div>
            </div>

            <p>{t('follow_soc')}</p>

            <div>
                <a href="https://www.facebook.com/KatedraTelesneVychovyASportuZcuVPlzni"><FaFacebookSquare color="#3b5998ff" size={35} /></a>
                <a href='https://www.instagram.com/kts.zcu/'><FaInstagramSquare color="#ea2c59ff" size={35} /></a>
            </div>

            <Image src={require('/assets/images/kts-sbor.jpg')} />
        </div>
    );
}

export default AboutKTS;