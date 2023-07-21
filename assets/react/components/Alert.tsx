import React from "react";
import { Alert as RBAlert, Button, Modal } from "react-bootstrap";

interface AProps {
    title: string;
    description?: string;
    onAccept?: () => void;
    onClose: () => void;
}

const Alert = (props: AProps) => (
    <Modal
        show={true}
        onHide={props.onClose}
        backdrop="static"
        keyboard={false}
    >
        <RBAlert show={true} variant="light" className="no-margin" >
            <RBAlert.Heading>{props.title}</RBAlert.Heading>
            <p>
                {props.description}
            </p>
            <hr />
            <div className="d-flex justify-content-end spa">
                <Button onClick={props.onAccept} variant="outline-dark">
                    Yes
                </Button>
                <Button onClick={props.onClose} variant="dark">
                    No
                </Button>
            </div>
        </RBAlert >
    </Modal>
);


export default Alert;