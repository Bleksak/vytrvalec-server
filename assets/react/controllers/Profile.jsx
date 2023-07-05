import React, {useEffect, useState} from "react";
import {useParams} from "react-router-dom";
import axios from "axios";

export default function Profile() {
    const {userId} = useParams();
    const [profile, setProfile] = useState(null);
    const [submissions, setSubmissions] = useState([]);

    const id = userId === undefined ? "" : userId;

    useEffect(() => {
        getUserData(id).then((data) => {
            setProfile(data);
        });

        console.log("getting submissions")

        getUserSubmissions(id).then((subs) => {
            setSubmissions(subs);
            console.log(subs);
        });

    }, []);

    return <>

    </>
}

async function getUserData(id) {
    return (await axios.get('/api/user/profile/' + id)).data;
}

async function getUserSubmissions(id) {
    return (await axios.get('/api/user/submissions/' + id)).data;
}