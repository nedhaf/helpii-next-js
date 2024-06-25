import React, { useState } from 'react';
import { Lightbox } from "react-modal-image-responsive";

const getPublicUrl = process.env['NEXT_PUBLIC_BACKEND_URL'];

const ImageComponent = ({ img }) => {
  const [lightboxOpen, setLightboxOpen] = useState(false);

  const handleImageClick = () => {
    setLightboxOpen(true);
  };

  const handleCloseLightbox = () => {
    setLightboxOpen(false);
  };

  return (
    <>
      <div
        className="shared-photo chat-image"
        style={{ backgroundImage: `url("${getPublicUrl}/storage/attachments/${img}")` }}
        onClick={handleImageClick}
      ></div>
      {lightboxOpen && (
        <Lightbox
          medium={`${getPublicUrl}/storage/attachments/${img}`}
          large={`${getPublicUrl}/storage/attachments/${img}`}
          alt="Hello World!"
          onClose={handleCloseLightbox}
        />

      )}
    </>
  );
};

export default ImageComponent;
