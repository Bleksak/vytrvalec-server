import React, { useEffect, useState } from "react";
import { useParams } from "react-router-dom";
import { getUserData, getUserSubmissions } from "../api/VytrvalecApi";
import { Button, Modal, Image } from 'react-bootstrap';
import { MdCheck, MdClose, MdQuestionMark } from "react-icons/md";

const Profile = () => {
    const { userId } = useParams();
    const [profile, setProfile] = useState(null);
    const [submissions, setSubmissions] = useState([]);
    const [selectedSubmission, setSelectedSubmission] = useState(null);

    const id = userId === undefined ? "" : userId;

    useEffect(() => {
        getUserData(id).then(setProfile);
        getUserSubmissions(1).then(setSubmissions);
    }, []);

    const handleSelectSubmission = (id) => {
        setSelectedSubmission(submissions.filter(sub => sub.id === id)[0]);
    }

    const renderIcon = () => {
        if (!selectedSubmission.reviewed) {
            return <MdQuestionMark />;
        }
        return selectedSubmission.accepted ? <MdCheck /> : <MdClose />;
    }

    return (
        <>
            {submissions.map(sub =>
                <Button key={sub.id} variant="primary" onClick={() => handleSelectSubmission(sub.id)}>
                    {new Date(sub.date).toDateString('cs-CZ')}
                </Button>
            )}

            {selectedSubmission &&
                <Modal
                    show={selectedSubmission != null}
                    onHide={() => setSelectedSubmission(null)}
                    backdrop="static"
                    keyboard={false}
                >
                    <Modal.Header closeButton>
                        <Modal.Title>
                            {new Date(selectedSubmission.date).toDateString('cs-CZ')} {selectedSubmission.activity.name} {/* FIXME */}
                        </Modal.Title>
                    </Modal.Header>

                    <Modal.Body>
                        <div className="container">
                            <div className="row">
                                <div className="col-sm"> {/* FIXME */}
                                    <h5>{selectedSubmission.activity.name}</h5>
                                    <p>Status: {renderIcon()}</p> {/* TODO trans */}
                                    <p>Distance: {selectedSubmission.distance} km</p>
                                    <p>Elevation: {selectedSubmission.elevation} m</p>
                                </div>
                                <div className="col-sm">
                                    <Image src={selectedSubmission.image} rounded />{/* TODO bigger picture */}
                                </div>
                            </div>
                        </div>
                    </Modal.Body>
                </Modal>
            }
        </>
    )
}

export default Profile;
