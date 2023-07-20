import { useTranslation } from "react-i18next";
import useAuth from "../../useAuth";
import React from "react";
import { Link } from "react-router-dom";
import {Button, Nav} from "react-bootstrap";

const UserNotLoggedIn = () => {
    const [t, _] = useTranslation();
    const { auth } = useAuth();

    if (auth) {
        return <></>;
    }

    return (
        <>
            <Nav.Item>
                <Nav.Link as={Link} to='/user/login'>
                    <Button type='button' variant='outline-dark'>{t('login')}</Button>
                </Nav.Link>
            </Nav.Item>

            <Nav.Item>
                <Nav.Link as={Link} to='/user/register'>
                    <Button type='button' variant='outline-dark'>{t('sign_up')}</Button>
                </Nav.Link>
            </Nav.Item>
        </>
    )
}

export default UserNotLoggedIn;