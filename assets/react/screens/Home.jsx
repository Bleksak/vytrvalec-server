import React, {useEffect, useState} from "react";
import {useTranslation} from "react-i18next";
import axios from "axios";
import {Carousel} from "react-bootstrap";

export default function Home() {

    const [t] = useTranslation();

    return <>
        <Logo/>
        <EventSummary/>
        <Carousel indicators={false} interval={null}>
            <Carousel.Item>
                <h2><strong>April 2023: 5 letý Nicolas</strong></h2>
                <div className='carousel-inside-item'>
                    <div className='carousel-row'>
                        <p><i className="fa-solid fa-crown gold mx-2"></i><strong>1. </strong>Fakulta A</p>
                        <p><i className="fa-solid fa-crown silver mx-2"></i><strong>2. </strong>Fakulta B</p>
                        <p><i className="fa-solid fa-crown bronze mx-2"></i><strong>3. </strong>Fakulta C</p>
                    </div>
                    <div className='carousel-row'>
                        <p>
                            V sedmi měsících mu byl diagnostikován nádor na mozku, který nešťastně postihnul křížení zrakových nervů. Po první operaci, kdy byl nádor částečně odstraněn, přišel bohužel Nicolas o zrak. Navíc byla tehdy zasažena hormonální část mozku, takže hormony jsou mu uměle několikrát denně podávány společně s léky na epilepsii, růst a momentálně i na ředění krve. Jeho léčba je finančně velice náročná. Pojďme mu aktivním sportováním pomoci!
                        </p>
                    </div>
                </div>
            </Carousel.Item>

            <Carousel.Item>
                <h2><strong>April 2023: 5 letý Nicolas</strong></h2>
                <div className='carousel-inside-item'>
                    <div className='carousel-row'>
                        <p><i className="fa-solid fa-crown gold mx-2"></i><strong>1. </strong>Fakulta A</p>
                        <p><i className="fa-solid fa-crown silver mx-2"></i><strong>2. </strong>Fakulta B</p>
                        <p><i className="fa-solid fa-crown bronze mx-2"></i><strong>3. </strong>Fakulta C</p>
                    </div>
                    <div className='carousel-row'>
                        <p>
                            V sedmi měsících mu byl diagnostikován nádor na mozku, který nešťastně postihnul křížení zrakových nervů. Po první operaci, kdy byl nádor částečně odstraněn, přišel bohužel Nicolas o zrak. Navíc byla tehdy zasažena hormonální část mozku, takže hormony jsou mu uměle několikrát denně podávány společně s léky na epilepsii, růst a momentálně i na ředění krve. Jeho léčba je finančně velice náročná. Pojďme mu aktivním sportováním pomoci!
                        </p>
                    </div>
                </div>
            </Carousel.Item>
        </Carousel>
    </>
}

function Logo() {
    const [t, _] = useTranslation();

    return <>
        <div className="main">
            <div className="col title">
                <h1>{t('title').toUpperCase()}</h1>
                <h2>{t('join_us')}</h2>
            </div>
        </div>
    </>
}

function EventSummary() {
    const [t, _] = useTranslation();

    const [participants, setParticipants] = useState(0);
    const [summary, setSummary] = useState({});

    useEffect(() => {
        getUserCount().then((res) => setParticipants(res.data));
        getSummaryDistance().then((res) => setSummary(res.data));
    }, []);

    return <>
        <div className="about-challenge">
            <h2><strong>{t('about_challenge')}</strong></h2>
            <div className="about-challenge-container">
                <p>{t('challenge_description_left')}</p>
                <p>{t('challenge_description_right')}</p>
            </div>

            <div className='summary-item'>
                <h3><b>{ participants }</b></h3>
                {t('participants')}
            </div>

        </div>

        <div className='summary'>

            { Object.keys(summary).map((activity) =>
                <div className='summary-item' key={activity}>
                    <h3><b>{ Math.ceil(summary[activity] / 1000)} km</b></h3>
                    {t(activity)}
                </div>
            ) }
        </div>
    </>
}

// function CarouselItem({content, active = false}) {
//     let clsName = `carousel-item${active ? " active" : ""}`;
//
//     return <div className={clsName}>
//         <p>
//             {content}
//         </p>
//     </div>
// }

const getUserCount = async() => {
    return await axios.get('/api/user/count');
}

const getSummaryDistance = async() => {
    return await axios.get('/api/summary/distances');
}