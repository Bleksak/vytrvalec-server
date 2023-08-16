import React from "react";
import { useTranslation } from "react-i18next";
import { Carousel as BCarousel } from "react-bootstrap";
import { FaCrown } from "react-icons/fa";

const Carousel = () => {
    const [t, _] = useTranslation();

    return (
        <BCarousel indicators={false} interval={null} className="no-padding no-margin">
            <BCarousel.Item>
                <h2><strong>April 2023: 5 letý Nicolas</strong></h2>
                <div className='carousel-inside-item'>
                    <div className='carousel-row'>
                        <p>
                            <FaCrown size={25} color='gold' className='mx-2' />
                            <strong>1. </strong>Fakulta A</p>
                        <p>
                            <FaCrown size={25} color='silver' className='mx-2' />
                            <strong>2. </strong>Fakulta B</p>
                        <p>
                            <FaCrown size={25} color='brown' className='mx-2' />
                            <strong>3. </strong>Fakulta C
                        </p>
                    </div>
                    <div className='carousel-row' style={{ marginLeft: '2%' }}>
                        <p>
                            V sedmi měsících mu byl diagnostikován nádor na mozku, který nešťastně postihnul křížení zrakových nervů. Po první operaci, kdy byl nádor částečně odstraněn, přišel bohužel Nicolas o zrak. Navíc byla tehdy zasažena hormonální část mozku, takže hormony jsou mu uměle několikrát denně podávány společně s léky na epilepsii, růst a momentálně i na ředění krve. Jeho léčba je finančně velice náročná. Pojďme mu aktivním sportováním pomoci!
                        </p>
                    </div>
                </div>
            </BCarousel.Item>

            <BCarousel.Item>
                <h2><strong>April 2023: 5 letý Nicolas</strong></h2>
                <div className='carousel-inside-item'>
                    <div className='carousel-row'>
                        <p>
                            <FaCrown size={25} color='gold' className='mx-2' />
                            <strong>1. </strong>Fakulta A</p>
                        <p>
                            <FaCrown size={25} color='silver' className='mx-2' />
                            <strong>2. </strong>Fakulta B</p>
                        <p>
                            <FaCrown size={25} color='brown' className='mx-2' />
                            <strong>3. </strong>Fakulta C
                        </p>
                    </div>
                    <div className='carousel-row'>
                        <p>
                            V sedmi měsících mu byl diagnostikován nádor na mozku, který nešťastně postihnul křížení zrakových nervů. Po první operaci, kdy byl nádor částečně odstraněn, přišel bohužel Nicolas o zrak. Navíc byla tehdy zasažena hormonální část mozku, takže hormony jsou mu uměle několikrát denně podávány společně s léky na epilepsii, růst a momentálně i na ředění krve. Jeho léčba je finančně velice náročná. Pojďme mu aktivním sportováním pomoci!
                        </p>
                    </div>
                </div>
            </BCarousel.Item>
        </BCarousel>
    )
}

export default Carousel;
