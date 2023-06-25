import React from "react";
import { useTranslation } from "react-i18next";

export default function Home() {
  return <>
    <Logo/>
    <EventSummary/>
    <Carousel/>
    </>
}

function Logo() {
  const [t, _ ] = useTranslation();

  return <>
    <div className="main">
      <div className="col title">
        <h1>{ t('title').toUpperCase() }</h1>
        <h2>{ t('join_us') }</h2>
      </div>
    </div>
  </>
}

function EventSummary() {
  const [t, _ ] = useTranslation();

  return <>
  <div className="about">
    <h2><strong>{t('about_challenge')}</strong></h2>
      <div dangerouslySetInnerHTML={
        {__html: t('challenge_description')}
      } />

    <div className="row py-4 px-5 text-center">
      <div className="col-md-4">
        <h3><b>10 </b></h3>
        {/*{# <h3><b>{{ users_count }}</b></h3> #}*/}
        { t('participants') }
      </div>
      <div className="col-md-4">
        <h3><b>20 km</b></h3>
        {/*{# <h3><b>{{ bike_km|floatformat:0 }}</b> km</h3> #}*/}
        { t('bike_and_scooter') }
      </div>

      <div className="col-md-4">
        <h3><b>200 km</b></h3>
        {/*{# <h3><b>{{ run_km|floatformat:0 }}</b> km</h3> #}*/}
        { t('run_and_walk') }
      </div>
    </div>
  </div>
  </>
}

function Carousel() {
  const [t, _ ] = useTranslation();

  return <>
    <div className="carousel-master light-blue-bg pb-3">
      <div className="flex-grow-1">
        <h4 className="text-center">{ t('winners') }</h4>
        <div className="slideshow blue-border white-bg">
          <div id="carousel-first" className="carousel slide">
            <div className="carousel-inner">
              <CarouselItem content="JSDFFLSKJDFKJLDSJKL" active={true}/>
              <CarouselItem content="JOJOJJOOOOO"/>
              <CarouselItem content="KOKOTKOOOO"/>
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
        <h4 className="text-center">{ t('winners') }</h4>
        <div className="slideshow blue-border white-bg">
          <div id="carousel-second" className="carousel slide">
            <div className="carousel-inner">
              <CarouselItem content="JSDFFLSKJDFKJLDSJKL" active={true}/>
              <CarouselItem content="JOJOJJOOOOO"/>
              <CarouselItem content="KOKOTKOOOO"/>
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
  </>
}

function CarouselItem({content, active = false}) {
  let clsName = `carousel-item${active ? " active" : ""}`;

  return <div className={clsName}>
    <p>
      {content}
    </p>
  </div>
}
