import React, {useEffect, useState} from "react";
import { useTranslation } from "react-i18next"
import {Link, useNavigate} from "react-router-dom";
import useAuth, {hasRole} from "./useAuth";
import axios from "axios";

export default function Navbar() {
    const [t, _] = useTranslation();

    const { user } = useAuth();

    return <nav className="navbar navbar-light navbar-expand-md bg-faded justify-content-center">
        <Link className="navbar-brand d-flex me-auto" to='/'>{ t('navbar_title') }</Link>
        <button className="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#collapsingNavbar3">
            <span className="navbar-toggler-icon"></span>
        </button>

        <div className="navbar-collapse collapse w-100" id="collapsingNavbar3">
            <ul className="nav navbar-nav ms-auto w-100 justify-content-end">
                <UserStaff/>
                <Everyone/>
                <UserNotLoggedIn/>
                <UserLoggedIn/>
            </ul>
        </div>
    </nav>
}

function Everyone() {
    const [t, _] = useTranslation();

    return <>
        <li className="nav-item">
            <Link className="nav-link" to='/rules'>{ t('rules') }</Link>
        </li>

        <li className="nav-item">
            <Link className="nav-link" to='/results'>{ t('navbar_results') }</Link>
        </li>
    </>
}

function UserNotLoggedIn() {
    const [t, _] = useTranslation();
    const {auth} = useAuth();

    if(auth !== false) {
        return <></>;
    }

    return <>
        <li className="nav-item">
            <Link to="/user/login">
                <button type="button" className="btn btn-outline-dark">{ t('login') }</button>
            </Link>
        </li>
        <li className="nav-item">
            <Link to="/user/register">
                <button type="button" className="btn btn-outline-dark">{ t('sign_up') }</button>
            </Link>
        </li>
    </>
}

function UserLoggedIn({}) {
    const navigate = useNavigate();
    const [t, _] = useTranslation();
    const { auth, setAuth, user } = useAuth();
    const [unlogged, setUnlogged] = useState(false);

    useEffect(() => {
        if(auth === false && unlogged === true) {
            navigate('/');
        }
    }, [unlogged]);

    if(auth === false) {
        return <></>;
    }

    const logout = async () => {
        await axios.get('/api/user/logout');
        setUnlogged(true);
        setAuth(false);
    }

    return <>
        <li className="nav-item">
            <Link className="nav-link" to="/user/profile"> { t('navbar_profile') } </Link>
        </li>

        <li className="nav-item">
            <Link className="nav-link" to="/submission/create">{ t('navbar_submit') }</Link>
        </li>

        <li className="nav-item">
            <button type="button" className="btn btn-outline-dark" onClick={logout}>
                { t('navbar_logout') }
            </button>
        </li>
    </>
}

function UserStaff() {
    const [t, _] = useTranslation();
    const {user, auth} = useAuth();

    if(auth === false || user == null) {
        return <></>;
    }

    if(!hasRole(user, "ROLE_STAFF")) {
        return <></>;
    }

    return <li className="nav-item dropdown">
        <a className="nav-link dropdown-toggle" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            { t('navbar_management') }
        </a>

        <ul className="dropdown-menu" aria-labelledby="navbarDropdown">
            <li>
                <Link className="dropdown-item" to="/management/users">{ t('navbar_user_management') }</Link>
            </li>
            <li>
                <Link className="dropdown-item" to="/management/seasons">{ t('navbar_season_management') }</Link>
            </li>
        </ul>
    </li>
}

