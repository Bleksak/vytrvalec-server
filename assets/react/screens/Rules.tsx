import React from "react";
import { useTranslation } from "react-i18next";

const Rules = () => {
    const [t, _] = useTranslation();

    const exclMark = <>
        <i className="fa-solid fa-exclamation"> </i><i className="fa-solid fa-exclamation"> </i><i className="fa-solid fa-exclamation"> </i>;
    </>

    return (
        <div className="rules">
            <div className="px-5 py-3">
                <h3>{t('rules')}</h3>
                <div dangerouslySetInnerHTML={
                    //@ts-ignore
                    { __html: t('rules_description') }
                } />
            </div>

            <div className='rules-rules blue-border white-bg'>
                <strong>{t('rules')}:</strong>
                <ol>
                    <li>
                        <span>{t('two_disciplines')}:</span>
                        <ul>
                            <li>{t('run_and_walk')}</li>
                            <li>{t('bike_and_scooter')}</li>
                        </ul>
                    </li>
                    <li>{t('rule2')}</li>
                </ol>

                <strong>{t('rating')}</strong>
                <p>{t('rating_description')}</p>
                <p dangerouslySetInnerHTML={
                    //@ts-ignore
                    { __html: t('extra_points') }
                } />

                <ul>
                    <li>{t('third_week')}
                        <ul>
                            <li>1 <strong>{t('extra_point')}</strong> {t('best_in_day')}.</li>
                            <li>2 <strong>{t('extra_point_plural')}</strong> {t('best_in_week')}.</li>
                        </ul>
                    </li>
                </ul>

                <ul>
                    <li>{t('fourth_week')}
                        <ul>
                            <li>1 <strong>{t('extra_point')}</strong> {t('best_elevation')}.</li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    )
}

export default Rules;
