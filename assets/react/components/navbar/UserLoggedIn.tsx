import { useTranslation } from "react-i18next";
import { Link, useNavigate } from "react-router-dom";
import useAuth from "../../useAuth";
import React, { useState, useEffect } from "react";
import { logout } from "../../api/UserApi";

const UserLoggedIn = () => {
    const navigate = useNavigate();
    const [t, _] = useTranslation();
    const { auth, setAuth } = useAuth();
    const [unlogged, setUnlogged] = useState<boolean>(false);

    useEffect(() => {
        if (auth === false && unlogged === true) {
            navigate('/');
        }
    }, [unlogged]);

    if (auth === false) {
        return <></>;
    }

    const handleLogout = async () => {
        await logout();
        setUnlogged(true);
        setAuth(false);
    }

    return (
        <>
            <li className="nav-item">
                <Link className="nav-link" to="/user/profile"> {t('navbar_profile')} </Link>
            </li>

            <li className="nav-item">
                <Link className="nav-link" to="/submission/create">{t('navbar_submit')}</Link>
            </li>

            <li className="nav-item">
                <button type="button" className="btn btn-outline-dark" onClick={handleLogout}>
                    {t('navbar_logout')}
                </button>
            </li>
        </>
    )
}

export default UserLoggedIn;
