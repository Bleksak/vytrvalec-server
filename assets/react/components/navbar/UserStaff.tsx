import {useTranslation} from "react-i18next";
import useAuth, {hasRole} from "../../useAuth";
import React from "react";
import {Link} from "react-router-dom";
import {Dropdown} from "react-bootstrap";

const UserStaff = () => {
    const [t, _] = useTranslation();
    const {user, auth} = useAuth();

    if (!auth || user == null) {
        return <></>;
    }

    if (!hasRole(user, "ROLE_STAFF")) {
        return <></>;
    }

    return <Dropdown>
        <Dropdown.Toggle variant='button' className='nav-link'>
            {t('navbar_management')}
        </Dropdown.Toggle>
        <Dropdown.Menu>
            <Dropdown.Item as={Link} to='/management/users'>
                {t('navbar_user_management')}
            </Dropdown.Item>
            <Dropdown.Item as={Link} to='/management/seasons'>
                {t('navbar_season_management')}
            </Dropdown.Item>
        </Dropdown.Menu>
    </Dropdown>
}

export default UserStaff;