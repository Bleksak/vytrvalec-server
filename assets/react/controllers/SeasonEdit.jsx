// import React from 'react';
import { useTranslation } from 'react-i18next';
import _ from '../i8n'
import React, { useEffect, useState } from "react";

export default function SeasonEdit({seasonId}) {
    const [season, setSeason] = useState(null);

    const [t, _ ] = useTranslation();

    useEffect(() => {
        fetchData(seasonId).then((response) => {
            setSeason(response);
        });
    }, []);

    return (
    <>
        <form>
            <input name="charityName" type="text"/>
            <textarea name="charityDescription"></textarea>
            <button type="button"></button>
        </form>
    </>
    )
}

const fetchData = async (id) => {
    const requestOptions = {
        method: 'GET',
        // headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        // body: 'user_id='+user.id
    };
    const response = await fetch('/api/season/'+id, requestOptions).catch(() => null);
    if(response == null) {
        return null;
    }

    return await response.json().catch(() => null);
}
