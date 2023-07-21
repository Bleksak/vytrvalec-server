import { Col, Modal, Row, Image, Button } from "react-bootstrap";
import React, { useState } from "react";
import { MdCheck, MdClose, MdQuestionMark } from "react-icons/md";
import Submission from "../../types/Submission";
import Alert from "../Alert";
import { deleteSubmission } from "../../api/SubmissionApi";

interface SMProps {
    submission: Submission;
    onClose: () => void;
}

const SubmissionModal = (props: SMProps): JSX.Element => {
    const [alertVisible, setAlertVisible] = useState<boolean>(false);
    const { submission } = props;

    const handleDeleteSubmission = (): void => {
        deleteSubmission(submission.id);
        setAlertVisible(true)
    }

    const renderIcon = (): JSX.Element => {
        if (!submission.reviewed) {
            return <MdQuestionMark color='gold' />;
        }
        return submission.accepted ? <MdCheck color="green" /> : <MdClose color="red" />;
    }

    return (
        <>
            <Modal
                show={true}
                onHide={props.onClose}
                backdrop="static"
                keyboard={false}
            >
                <Modal.Header closeButton className="modal-header" >
                    <Modal.Title> {submission.activity.name}</Modal.Title>
                </Modal.Header>

                <Modal.Body className="no-padding" >
                    <Row>
                        <Col sm className="no-padding no-margin">
                            <Image src={submission.image} rounded />
                        </Col>
                        <Col sm > {/* FIXME */}
                            <Col style={{ height: '100%', justifyContent: 'space-between' }}>
                                <Row>
                                    <p>Date: {new Date(submission.date).toDateString()}</p>
                                    <p>Status: {renderIcon()}</p> {/* TODO trans */}
                                    <p>Distance: {submission.distance} km</p>
                                    <p>Elevation: {submission.elevation} m</p>
                                    {submission.comment && <p>Comment: {submission.comment}</p>}
                                </Row>
                                <Row >
                                    {!submission.reviewed && <Button
                                        onClick={() => setAlertVisible(true)}
                                        style={{ borderRadius: 20 }}
                                        variant='danger' >
                                        Delete submission
                                        <MdClose size={20} />
                                    </Button>}
                                </Row>
                            </Col>
                        </Col>
                    </Row>
                </Modal.Body>
            </Modal >

            {alertVisible && <Alert
                title="Delete submission"
                description="Are you sure you want to delete this submission?"
                onAccept={handleDeleteSubmission}
                onClose={() => setAlertVisible(false)}
            />}
        </>
    )
}


export default SubmissionModal;