import React from "react";
import { useTranslation } from "react-i18next";

const Carousel = () => {
    const [t, _] = useTranslation();

    return (
        <div className="carousel-master light-blue-bg pb-3">
            <div className="flex-grow-1">
                <h4 className="text-center">{t('winners')}</h4>
                <div className="slideshow blue-border white-bg">
                    <div id="carousel-first" className="carousel slide">
                        <div className="carousel-inner">
                            <CarouselItem content="JSDFFLSKJDFKJLDSJKL" active={true} />
                            <CarouselItem content="JOJOJJOOOOO" />
                            <CarouselItem content="KOKOTKOOOO" />
                        </div>

                        <button className="carousel-control-prev" type="button" data-bs-target="#carousel-first" data-bs-slide="prev">
                            <span className="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span className="visually-hidden">Previous</span>
                        </button>

                        <button className="carousel-control-next" type="button" data-bs-target="#carousel-first" data-bs-slide="next">
                            <span className="carousel-control-next-icon" aria-hidden="true"></span>
                            <span className="visually-hidden">Next</span>
                        </button>
                    </div>
                </div>
            </div>

            <div className="flex-grow-1">
                <h4 className="text-center">{t('winners')}</h4>
                <div className="slideshow blue-border white-bg">
                    <div id="carousel-second" className="carousel slide">
                        <div className="carousel-inner">
                            <CarouselItem content="JSDFFLSKJDFKJLDSJKL" active={true} />
                            <CarouselItem content="JOJOJJOOOOO" />
                            <CarouselItem content="KOKOTKOOOO" />
                        </div>

                        <button className="carousel-control-prev" type="button" data-bs-target="#carousel-second" data-bs-slide="prev">
                            <span className="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span className="visually-hidden">Previous</span>
                        </button>
                        <button className="carousel-control-next" type="button" data-bs-target="#carousel-second" data-bs-slide="next">
                            <span className="carousel-control-next-icon" aria-hidden="true"></span>
                            <span className="visually-hidden">Next</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    )
}

export default Carousel;

const CarouselItem = ({ content, active = false }: { content: string, active?: boolean }) => {
    let clsName = `carousel-item${active ? " active" : ""}`;

    return (
        <div className={clsName}>
            <p>
                {content}
            </p>
        </div>
    )
}
