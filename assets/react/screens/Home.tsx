import React from "react";
import { Col, Row } from "react-bootstrap";
import { Carousel, EventSummary, Logo } from "../components/home";
import AboutKTS from "../components/AboutKTS";

const Home = () => (
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
);

export default Home;
