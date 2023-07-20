import { useTranslation } from "react-i18next";
import React from "react";
import { Link } from "react-router-dom";
import {Nav} from "react-bootstrap";

const Everyone = () => {
    const [t, _] = useTranslation();

    return <>
        <Nav.Item>
            <Nav.Link as={Link} to='/rules'>
                {t('rules')}
            </Nav.Link>
        </Nav.Item>

        <Nav.Item>
            <Nav.Link as={Link} to='/results'>
                {t('navbar_results')}
            </Nav.Link>
        </Nav.Item>
    </>
}
export default Everyone;