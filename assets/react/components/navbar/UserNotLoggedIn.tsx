import { useTranslation } from "react-i18next";
import useAuth from "../../useAuth";
import React from "react";
import { Link } from "react-router-dom";

const UserNotLoggedIn = () => {
    const [t, _] = useTranslation();
    const { auth } = useAuth();

    if (auth !== false) {
        return <></>;
    }

    return (
        <>
            <li className="nav-item">
                <Link to="/user/login">
                    <button type="button" className="btn btn-outline-dark">{t('login')}</button>
                </Link>
            </li>
            <li className="nav-item">
                <Link to="/user/register">
                    <button type="button" className="btn btn-outline-dark">{t('sign_up')}</button>
                </Link>
            </li>
        </>
    )
}

export default UserNotLoggedIn;