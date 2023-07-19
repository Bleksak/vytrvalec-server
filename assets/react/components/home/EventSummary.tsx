import React, { useEffect, useState } from "react";
import { useTranslation } from "react-i18next";
import { getUserCount } from "../../api/UserApi";
import { getSummaryDistance } from "../../api/SummaryApi";

const EventSummary = () => {
    const [t, _] = useTranslation();

    const [participants, setParticipants] = useState<number>(0);
    const [summary, setSummary] = useState({});

    useEffect(() => {
        getUserCount().then((res) => setParticipants(res.data));
        getSummaryDistance().then((res) => setSummary(res.data));
    }, []);

    return (
        <>
            <div className="about-challenge">
                <h2><strong>{t('about_challenge')}</strong></h2>
                <div className="about-challenge-container">
                    <p>{t('challenge_description_left')}</p>
                    <p>{t('challenge_description_right')}</p>
                </div>

            </div>

            <div className="summary">
                <div className="row py-4 px-5 text-center">
                    <div className="col-md-4">
                        <h3><b>{participants}</b></h3>
                        {t('participants')}
                    </div>

                    {Object.keys(summary).map((activity) =>
                        <div key={activity} className='col-md-4'>
                            {/* @ts-ignore */}
                            <h3><b>{summary[activity] / 1000} km</b></h3>
                            {t(activity)}
                        </div>
                    )}
                </div>
            </div>
        </>
    )
}

export default EventSummary;
