import { useTranslation } from "react-i18next";
import useAuth, { hasRole } from "../../useAuth";
import React from "react";
import { Link } from "react-router-dom";

const UserStaff = () => {
    const [t, _] = useTranslation();
    const { user, auth } = useAuth();

    if (auth === false || user == null) {
        return <></>;
    }

    if (!hasRole(user, "ROLE_STAFF")) {
        return <></>;
    }

    return <li className="nav-item dropdown">
        <a className="nav-link dropdown-toggle" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            {t('navbar_management')}
        </a>

        <ul className="dropdown-menu" aria-labelledby="navbarDropdown">
            <li>
                <Link className="dropdown-item" to="/management/users">{t('navbar_user_management')}</Link>
            </li>
            <li>
                <Link className="dropdown-item" to="/management/seasons">{t('navbar_season_management')}</Link>
            </li>
        </ul>
    </li>
}

export default UserStaff;