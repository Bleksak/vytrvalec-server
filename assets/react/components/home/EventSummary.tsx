import React from "react";
import { useTranslation } from "react-i18next";

const EventSummary = () => {
    const [t, _] = useTranslation();

    return (
        <div className="about">
            <h2><strong>{t('about_challenge')}</strong></h2>
            <div dangerouslySetInnerHTML={
                //@ts-ignore
                { __html: t('challenge_description') }
            } />

            <div className="row py-4 px-5 text-center">
                <div className="col-md-4">
                    <h3><b>10 </b></h3>
                    {/*{# <h3><b>{{ users_count }}</b></h3> #}*/}
                    {t('participants')}
                </div>
                <div className="col-md-4">
                    <h3><b>20 km</b></h3>
                    {/*{# <h3><b>{{ bike_km|floatformat:0 }}</b> km</h3> #}*/}
                    {t('bike_and_scooter')}
                </div>

                <div className="col-md-4">
                    <h3><b>200 km</b></h3>
                    {/*{# <h3><b>{{ run_km|floatformat:0 }}</b> km</h3> #}*/}
                    {t('run_and_walk')}
                </div>
            </div>
        </div>
    )
}

export default EventSummary;
