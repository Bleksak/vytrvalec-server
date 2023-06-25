import useAuth from "./useAuth";
import React, {useRef} from "react";
import {redirect, useLocation, useNavigate} from "react-router-dom";
import {useTranslation} from "react-i18next";
import axios from "axios";

export default function Login() {
    const navigate = useNavigate();
    const { auth } = useAuth()

    if(auth) {
        return navigate("/");
    }

    return <>
        <LoginForm/>
    </>
}

function LoginForm() {
    const [t, _] = useTranslation();
    const navigate = useNavigate();
    const { setAuth } = useAuth();

    const usernameRef = useRef();
    const passwordRef = useRef();

    const formSubmit = (ev) => {
        ev.preventDefault();

        const username = usernameRef.current.value;
        const password = passwordRef.current.value;

        sendLogin(username, password).then(data => {
            if(data.success) {
                setAuth(data.user);
                navigate("/");
            }
        });
    }

    return <div className="login">
        <form className="black-form" method="POST" action="/user/login" onSubmit={formSubmit}>
            <label htmlFor="username">{ t('email') }</label>
            <input className="form-control" id="username" type="text" name="_username" ref={usernameRef}/>
            <label htmlFor="password">{ t('password') }</label>
            <input className="form-control" id="password" type="password" name="_password" ref={passwordRef}/>

            <div className="d-flex justify-content-center">
                <button className="btn btn-primary" type="submit">{ t('login') }</button>
            </div>
        </form>
    </div>
}

async function sendLogin(username, password) {
    const result = await axios.post('/api/user/login', {
        username: username,
        password: password,
    });

    return result.data;
}
