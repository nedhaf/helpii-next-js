import React, { useState } from 'react';

const CountryDropdownOLD = ({ countries }) => {
  const [isOpen, setIsOpen] = useState(false);
  const [selectedCountry, setSelectedCountry] = useState(null);

  const toggleDropdown = () => setIsOpen(!isOpen);
  const handleSelect = (country) => {
    setSelectedCountry(country);
    setIsOpen(false);
  };

  return (
    <div className="country-dropdown">
      <button onClick={toggleDropdown}>
        {selectedCountry ? (
          <>
            <img src={selectedCountry.flag} alt={selectedCountry.name} />
            <span>{selectedCountry.name}</span>
          </>
        ) : (
          'Select Country'
        )}
      </button>
      {isOpen && (
        <ul>
          {countries.map((country) => (
            <li key={country.code} onClick={() => handleSelect(country)}>
              <img src={country.flag} alt={country.name} />
              <span>{country.name}</span>
            </li>
          ))}
        </ul>
      )}
    </div>
  );
};

export default CountryDropdown;
