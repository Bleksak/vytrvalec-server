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
                onExit={props.onClose}
            >
                <Modal.Header closeButton >
                    <Modal.Title> {submission.activity.name}</Modal.Title>
                </Modal.Header>

                <Modal.Body style={{ padding: 0, }} >
                    <Row >
                        <Col sm className="no-padding no-margin">
                            <Image src={submission.image} />
                        </Col>
                        <Col sm > {/* FIXME */}
                            <Col style={{ height: '100%', padding: '3%' }}>
                                <Row style={{ boxShadow: '1px 2px 9px gray', padding: '3%', height: '100%', justifyContent: 'stretch' }}>
                                    <div>
                                        <p>Date: {new Date(submission.date).toDateString()}</p>
                                        <p>Status: {renderIcon()}</p> {/* TODO trans */}
                                        <p>Distance: {submission.distance} km</p>
                                        <p>Elevation: {submission.elevation} m</p>
                                        {submission.comment && <p>Comment: {submission.comment}</p>}
                                    </div>
                                    <div style={{ alignContent: 'flex-end', display: 'grid' }}>
                                        {!submission.reviewed && <Button
                                            onClick={() => setAlertVisible(true)}
                                            // style={{ borderRadius: 20, marginTop: '5%' }}
                                            variant='danger' >
                                            Delete submission
                                            <MdClose size={20} />
                                        </Button>}
                                    </div>

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