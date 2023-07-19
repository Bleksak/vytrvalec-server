import { useTranslation } from "react-i18next";
import React from "react";
import { Link } from "react-router-dom";

const Everyone = () => {
    const [t, _] = useTranslation();

    return <>
        <li className="nav-item">
            <Link className="nav-link" to='/rules'>{t('rules')}</Link>
        </li>

        <li className="nav-item">
            <Link className="nav-link" to='/results'>{t('navbar_results')}</Link>
        </li>
    </>
}
export default Everyone;