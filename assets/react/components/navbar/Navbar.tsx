import React from "react";
import { useTranslation } from "react-i18next"
import { Link } from "react-router-dom";
import UserStaff from "./UserStaff";
import Everyone from "./Everyone";
import UserNotLoggedIn from "./UserNotLoggedIn";
import UserLoggedIn from "./UserLoggedIn";
import { Nav, Navbar } from "react-bootstrap";

const CustomNavbar = () => {
    const [t, _] = useTranslation();

    return (
        <Navbar expand='md' variant='light' bg='faded' style={{ marginRight: '2%', marginLeft: '2%' }}>
            <Navbar.Brand as={Link} to='/'>
                {t('navbar_title')}
            </Navbar.Brand>
            <Navbar.Toggle aria-controls='basic-navbar-nav' />
            <Navbar.Collapse id='basic-navbar-nav'>
                <Nav>
                    <UserStaff />
                    <Everyone />
                    <UserNotLoggedIn />
                    <UserLoggedIn />
                </Nav>
            </Navbar.Collapse>
        </Navbar>
    )
}

export default CustomNavbar;







