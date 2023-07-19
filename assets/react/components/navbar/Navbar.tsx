import React from "react";
import { useTranslation } from "react-i18next"
import { Link } from "react-router-dom";
import UserStaff from "./UserStaff";
import Everyone from "./Everyone";
import UserNotLoggedIn from "./UserNotLoggedIn";
import UserLoggedIn from "./UserLoggedIn";

const Navbar = () => {
    const [t, _] = useTranslation();

    return (
        <nav className="navbar navbar-light navbar-expand-md bg-faded justify-content-center">
            <Link className="navbar-brand d-flex me-auto" to='/'>{t('navbar_title')}</Link>
            <button className="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#collapsingNavbar3">
                <span className="navbar-toggler-icon"></span>
            </button>

            <div className="navbar-collapse collapse w-100" id="collapsingNavbar3">
                <ul className="nav navbar-nav ms-auto w-100 justify-content-end">
                    <UserStaff />
                    <Everyone />
                    <UserNotLoggedIn />
                    <UserLoggedIn />
                </ul>
            </div>
        </nav>
    )
}

export default Navbar;







