-- ============================================================================
-- CAF Master Data — bilingual (EN/HI) dropdown values
-- Source: "Master Data" sheet of CAF Data Model - Improved (2).xlsx
-- ============================================================================

SET NAMES utf8mb4;

-- m_business_profession_codes (01-20)
INSERT INTO m_business_profession_codes (code, label_en, label_hi) VALUES
('01','Medical Profession and Business','चिकित्सा संबंधी व्यवसाय और कारबार'),
('02','Engineering','इंजीनियरिंग'),
('03','Architecture','वास्तुकला'),
('04','Chartered Accountant / Accountancy','चार्टर्ड अकाउंटेंट / अकाउंटेंसी'),
('05','Interior Decoration','इंटीरियर डेकोरेशन'),
('06','Technical Consultancy','तकनीकी परामर्श कार्य'),
('07','Company Secretary','कंपनी सचिव'),
('08','Legal Practitioner and Solicitors','विधि व्यवसायी और सालिसिटर्स'),
('09','Government Contractors','सरकारी ठेकेदार'),
('10','Insurance Agency','बीमा एजेंट'),
('11','Films, TV and such other entertainment','फिल्म, टी.वी. और अन्य मनोरंजन'),
('12','Information Technology','सूचना प्रौद्योगिकी'),
('13','Builders and Developers','बिल्डर्स एवं डेवलपर्स'),
('14','Members of Stock Exchange, Share Brokers and Sub-Brokers','स्टॉक एक्सचेंज के सदस्य, शेयर दलाल और उप-दलाल'),
('15','Performing Arts and Yatra','प्रदर्शन कलाएँ'),
('16','Operation of Ships, Hovercraft, Aircrafts or Helicopters','जहाजों, होवरक्राफ्ट, एयरक्राफ्ट या हेलिकॉप्टर का संचालन'),
('17','Plying Taxis, Lorries, Trucks, Buses or other Commercial Vehicles','टैक्सी, लॉरी, ट्रक, बसें या अन्य वाणिज्यिक वाहन'),
('18','Ownership of Horses or Jockeys','घोड़ों का स्वामित्व या जॉकी'),
('19','Cinema Halls and Other Theatres','सिनेमा हॉल या अन्य थियेटर'),
('20','Others','अन्य');

-- m_countries (with ISD codes; ISO/FATF placeholder per source)
INSERT INTO m_countries (iso2, iso3, isd_code, label_en, label_hi, display_order) VALUES
('IN','IND','91','India','भारत',1),
('US','USA','1','United States','संयुक्त राज्य अमेरिका',2),
('GB','GBR','44','United Kingdom','यूनाइटेड किंगडम',3),
('SG','SGP','65','Singapore','सिंगापुर',4),
('AE','ARE','971','United Arab Emirates','संयुक्त अरब अमीरात',5),
('MU','MUS','230','Mauritius','मॉरिशस',6),
('LU','LUX','352','Luxembourg','लक्ज़मबर्ग',7);

-- m_titles
INSERT INTO m_titles (code, label_en, label_hi) VALUES
('MR','Shri/Mr.','श्री'),
('MRS','Smt/Mrs.','श्रीमती'),
('MS','Kumari/Ms.','कुमारी/सुश्री'),
('MS_CORP','M/s','मैससर्');

-- m_genders
INSERT INTO m_genders (code, label_en, label_hi) VALUES
('MALE','Male','पुरुष'),
('FEMALE','Female','महिला'),
('TRANS','Transgender','ट्रांसजेंडर');

-- m_marital_statuses
INSERT INTO m_marital_statuses (code, label_en, label_hi) VALUES
('SINGLE','Single','अविवाहित'),
('MARRIED','Married','विवाहित'),
('DIVORCED','Divorced','तलाकशुदा'),
('WIDOW','Widow/Widower','विधवा/विधुर');

-- m_citizenship_statuses
INSERT INTO m_citizenship_statuses (code, label_en, label_hi) VALUES
('FOREIGNER','Foreigner','विदेशी'),
('PIO','Person of Indian origin','भारतीय मूल का व्यक्ति'),
('OCI','Overseas citizen of India','भारत का विदेशी नागरिक');

-- m_income_sources
INSERT INTO m_income_sources (code, label_en, label_hi) VALUES
('SALARY','Salary','वेतन'),
('CAP_GAINS','Capital Gains','पूँजीगत अभिलाभ'),
('BUSINESS','Income from Business/Profession','कारबार/व्यवसाय से आय'),
('NO_INCOME','No Income','कोई आय नहीं'),
('OTHER','Income from other Sources','अन्य स्रोतों से आय'),
('HOUSE_PROP','Income from House Property','गृह संपत्ति से आय');

-- m_relationship_types
INSERT INTO m_relationship_types (code, label_en, label_hi) VALUES
('COMMON_OWN','Common Ownership','समान स्वामित्व'),
('COMMON_CTRL','Common Control','समान नियंत्रण'),
('OTHER','Other','अन्य');

-- m_operation_modes
INSERT INTO m_operation_modes (code, label_en, label_hi) VALUES
('ANY_ONE','Any one single','कोई भी एक'),
('JOINTLY','Jointly by','संयुक्त रूप से'),
('RESOLUTION','As per resolution','संकल्प के अनुसार'),
('OTHERS','Others','अन्य');

-- m_application_statuses
INSERT INTO m_application_statuses (code, label_en, label_hi) VALUES
('DRAFT','Draft','प्रारूप'),
('SUBMITTED','Submitted','प्रस्तुत'),
('UNDER_REVIEW','Under Review','समीक्षाधीन'),
('APPROVED','Approved','अनुमोदित'),
('REJECTED','Rejected','अस्वीकृत');

-- m_card_name_prefs
INSERT INTO m_card_name_prefs (code, label_en, label_hi) VALUES
('FATHER','Father''s Name','पिता का नाम'),
('MOTHER','Mother''s Name','माता का नाम');

-- m_risk_categories
INSERT INTO m_risk_categories (code, label_en, label_hi) VALUES
('LOW','Low','निम्न'),
('MEDIUM','Medium','मध्यम'),
('HIGH','High','उच्च');

-- m_occupation_codes (Individual 1-10, Non-Individual R/U/D/S/N/C)
INSERT INTO m_occupation_codes (applies_to, code, label_en, label_hi) VALUES
('Individual','1','Service - Private sector','सेवा - निजी क्षेत्र'),
('Individual','2','Service - Public Sector','सेवा - सार्वजनिक क्षेत्र'),
('Individual','3','Service - Govt. service','सेवा - सरकारी सेवा'),
('Individual','4','Business','कारबार'),
('Individual','5','Professional','व्यवसायी'),
('Individual','6','Agriculturist','कृषक'),
('Individual','7','Retired','सेवानिवृत्त'),
('Individual','8','Housewife','गृहिणी'),
('Individual','9','Student','छात्र'),
('Individual','10','Others','अन्य'),
('Non-Individual','R','Private Company','निजी कंपनी'),
('Non-Individual','U','Public Company','सार्वजनिक कंपनी'),
('Non-Individual','D','Body Corporate','निगमित निकाय'),
('Non-Individual','S','Financial Institution','वित्तीय संस्था'),
('Non-Individual','N','Non-Government Organisation','गैर-सरकारी संगठन'),
('Non-Individual','C','Charitable Organisation','धर्मार्थ संगठन');
