import React, {useEffect, useRef, useState} from "react";
import useAuth from "./useAuth";
import {useNavigate} from "react-router-dom";
import {useTranslation} from "react-i18next";
import axios from "axios";


export default function Registration() {
    const navigate = useNavigate();
    const { auth } = useAuth();
    const {t} = useTranslation();
    const [faculties, setFaculties] = useState(null);
    const [, setTooltip] = useState(null);

    const  gdpr = useRef(null);

    const emailRef = useRef();
    const passwordRef = useRef();
    const password2Ref = useRef();
    const firstNameRef = useRef();
    const lastNameRef = useRef();
    const facultyRef = useRef();

    useEffect(() => {
            if(auth) {
                return navigate("/");
            }
        }, [auth]
    );

    useEffect(() => {
        fetchFaculties().then((data) => {
            setFaculties(data);
        });
        // TODO: CATCH?
    }, []);

    useEffect(() => {
        const bootstrap = require('bootstrap');
        if(gdpr !== undefined) {
            setTooltip(new bootstrap.Tooltip(gdpr.current, {animation: true}));
        } else {
            setTooltip(null);
        }
    }, [gdpr]);

    const submit = (ev) => {

        if(!gdpr.current.checked) {
            // TODO: gdpr unchecked error
        }

        if(passwordRef.current.value !== password2Ref.current.value) {
            // TODO: passwords do not match error
        }

        if(!faculties.map((faculty) => faculty.id).includes(facultyRef.current.value)) {
            // TODO: Invalid faculty selected
        }

        sendRegister({
            firstName: firstNameRef.current.value,
            lastName: lastNameRef.current.value,
            email: emailRef.current.value,
            password: passwordRef.current.value,
            faculty: facultyRef.current.value,
        }).then((response) => {
            console.log(response);
        }).catch((error) => {
            // TODO: server side errors
            console.log('Errooooor');
        });

        ev.preventDefault();
    }

    return <>
        <div className="register">
            <h2 className="form-header text-center">{ t('sign_up') }</h2>

            <form className="black-form" method="POST" onSubmit={submit}>
                {/* TODO: TADY ERRORY*/}

                <label htmlFor="email">{ t('email') }</label>
                <input ref={emailRef} type="email" name="email" id="email" className="form-control"/>
                {/*{{form_errors(form.first_name)}}*/}

                <div className="d-flex gap-3">
                    <div>
                        {/* TODO: Error ke kazdemu fieldu zvlast */}
                        <label htmlFor="first_name">{ t('first_name') }</label>
                        <input ref={firstNameRef} type="text" name="first_name" id="first_name" className="form-control d-inline"/>
                        {/*{{form_errors(form.first_name)}}*/}
                    </div>

                    <div>
                        <label htmlFor="last_name">{ t('last_name') }</label>
                        <input ref={lastNameRef} type="text" name="last_name" id="last_name" className="form-control d-inline"/>
                        {/*{{form_errors(form.last_name)}}*/}
                    </div>
                </div>

                <label htmlFor="password">{ t('password') }</label>
                <input ref={passwordRef} type="password" name="password" id="password" className="form-control"/>
                {/*{{form_errors(form.last_name)}}*/}

                <label htmlFor="password_repeat">{ t('password_repeat') }</label>
                <input ref={password2Ref} type="password" name="password_repeat" id="password_repeat" className="form-control"/>

                <select ref={facultyRef} className="form-control">
                    { faculties && faculties.map( (faculty) =>
                        <option key={faculty.id} value={faculty.id}>{ faculty.name }</option>
                    )}
                </select>

                <label htmlFor="gdpr">{ t('gdpr_label') }</label>
                <input ref={gdpr} type="checkbox" name="gdpr" id="gdpr" className="form-check-input border border-2 border-danger" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title={ t('gdpr_tooltip') }/>

                <div className="d-flex justify-content-center">
                    <button type="submit" className="btn btn-primary d-block mx-1 mt-2 mb-1">{ t('sign_up') }</button>
                </div>

            </form>
        </div>
    </>
}

async function fetchFaculties() {
    return (await axios.get('/api/faculty/list')).data;
}

async function sendRegister(data) {
    return (await axios.post('/api/user/register', data));
}
