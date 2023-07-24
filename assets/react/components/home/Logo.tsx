import React from "react";
import { useTranslation } from "react-i18next";

const Logo = () => {
    const [t, _] = useTranslation();

    return <>
        <header>
            <div>
                <h1>{t('title').toUpperCase()}</h1>
                <h2>{t('join_us')}</h2>
            </div>
        </header>
    </>
}
export default Logo;