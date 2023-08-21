import React, { useEffect, useState } from "react";
import { useTranslation } from "react-i18next";
import { getUserCount } from "../../api/UserApi";
import { getSummaryDistance } from "../../api/SummaryApi";

const EventSummary = () => {
    const [t, _] = useTranslation();

    const [participants, setParticipants] = useState(0);
    const [summary, setSummary] = useState({});

    useEffect(() => {
        getUserCount().then(setParticipants);
        // getSummaryDistance().then(setSummary);
    }, []);

    // @ts-ignore
    return <>
        <div className="about-challenge">
            <h2><strong>{t('about_challenge')}</strong></h2>
            <div className="about-challenge-container">
                <p>{t('challenge_description_left')}</p>
                <p>{t('challenge_description_right')}</p>
            </div>


        </div>


        <div className='summary'>
            <div className='summary-item'>
                <h3><b>{participants}</b></h3>
                {t('participants')}
            </div>

            {Object.keys(summary).map((activity) =>
                <div className='summary-item' key={activity}>
                    {/* @ts-ignore */}
                    <h3><b>{Math.ceil(summary[activity] / 1000)} km</b></h3>
                    {t(activity)}
                </div>
            )}
        </div>
    </>
}
export default EventSummary;
