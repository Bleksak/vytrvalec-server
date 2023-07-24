import React from "react";
import { useTranslation } from "react-i18next";
import { Col, Row } from "react-bootstrap";
import { Carousel, EventSummary, Logo } from "../components/home";
import AboutKTS from "../components/AboutKTS";

const Home = () => {
	const [t] = useTranslation();

	return (
		<>
			<Logo />
			<Row>
				<Col className="col-lg-4">
					<AboutKTS />
				</Col>
				<Col className="bg-blue">
					<div className="container-new" >
						<EventSummary />
					</div>
					<div className="container-new" >
						<Carousel />
					</div>

				</Col>
			</Row>
		</>
	)

}

export default Home;


