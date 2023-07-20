import {useTranslation} from "react-i18next";
import {Link, useNavigate} from "react-router-dom";
import useAuth from "../../useAuth";
import React, {useState, useEffect} from "react";
import {logout} from "../../api/UserApi";
import {Button, Nav} from "react-bootstrap";

const UserLoggedIn = () => {
    const navigate = useNavigate();
    const [t, _] = useTranslation();
    const {auth, setAuth} = useAuth();
    const [unlogged, setUnlogged] = useState<boolean>(false);

    useEffect(() => {
        if (!auth && unlogged) {
            navigate('/');
        }
    }, [unlogged]);

    if (!auth) {
        return <></>;
    }

    const handleLogout = async () => {
        await logout();
        setUnlogged(true);
        setAuth(false);
    }

    return (
        <>
            <Nav.Item>
                <Nav.Link as={Link} className='nav-item' to='/user/profile'>
                    {t('navbar_profile')}
                </Nav.Link>
            </Nav.Item>

            <Nav.Item>
                <Nav.Link as={Link} to='/submission/create'>
                    {t('navbar_submit')}
                </Nav.Link>
            </Nav.Item>

            <Nav.Item>
                <Nav.Link as={Button} bsPrefix=' ' type='button' variant='outline-dark' onClick={handleLogout}>
                    {t('navbar_logout')}
                </Nav.Link>
            </Nav.Item>
        </>
    )
}

export default UserLoggedIn;
