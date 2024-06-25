import React, { useState, useRef, useEffect } from 'react';
import 'bootstrap/dist/js/bootstrap.bundle.min';

const CustomClockpicker = ({ onChange, defaultValue }) => {
  const clockInputRef = useRef(null);
  const [selectedTime, setSelectedTime] = useState(defaultValue || '');

  useEffect(() => {
    // Initialize clockpicker on component mount
    const clockpicker = new window.ClockPicker(clockInputRef.current, {
      placement: 'bottom', // Adjust placement as needed
      align: 'left', // Adjust alignment as needed
      donetext: 'Done', // Customize done button text
    });

    clockpicker.setValue(selectedTime); // Set initial value

    return () => {
      // Clean up clockpicker instance on unmount
      clockpicker.destroy();
    };
  }, [selectedTime]);

  const handleTimeChange = (event) => {
    setSelectedTime(event.target.value);
    if (onChange) {
      onChange(event.target.value); // Call the provided onChange handler
    }
  };

  return (
    <div className="input-group clockpicker">
      <input
        type="text"
        className="form-control"
        value={selectedTime}
        onChange={handleTimeChange}
        ref={clockInputRef}
        placeholder="Select Time"
      />
      <span className="input-group-addon">
        <span className="glyphicon glyphicon-time"></span>
      </span>
    </div>
  );
};

export default CustomClockpicker;