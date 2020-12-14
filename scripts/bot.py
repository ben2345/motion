#!/usr/bin/python

import time
import math
import smbus

# ============================================================================
# Raspi PCA9685 16-Channel PWM Servo Driver
# ============================================================================

class PCA9685:

  # Registers/etc.
  __SUBADR1            = 0x02
  __SUBADR2            = 0x03
  __SUBADR3            = 0x04
  __MODE1              = 0x00
  __PRESCALE           = 0xFE
  __LED0_ON_L          = 0x06
  __LED0_ON_H          = 0x07
  __LED0_OFF_L         = 0x08
  __LED0_OFF_H         = 0x09
  __ALLLED_ON_L        = 0xFA
  __ALLLED_ON_H        = 0xFB
  __ALLLED_OFF_L       = 0xFC
  __ALLLED_OFF_H = 0xFD
  
  ROTATION_SERVO_ID    = 0
  ROTATION_POS_BASE    = 1500
  ROTATION_POS_CURRENT = 500

  LIFT_SERVO_ID    = 5
  LIFT_POS_BASE    = 1400
  LIFT_POS_CURRENT = 1400

  DEPLOY_SERVO_ID    = 15
  DEPLOY_POS_BASE    = 2400
  DEPLOY_POS_CURRENT = 2400

  PINCH_SERVO_ID    = 10
  PINCH_POS_BASE    = 2500
  PINCH_POS_CURRENT = 2500

  def __init__(self, address=0x40, debug=False):
    self.bus = smbus.SMBus(1)
    self.address = address
    self.debug = debug
    if (self.debug):
      print("Reseting PCA9685")
    self.write(self.__MODE1, 0x00)
	
  def write(self, reg, value):
    "Writes an 8-bit value to the specified register/address"
    self.bus.write_byte_data(self.address, reg, value)
    if (self.debug):
      print("I2C: Write 0x%02X to register 0x%02X" % (value, reg))
	  
  def read(self, reg):
    "Read an unsigned byte from the I2C device"
    result = self.bus.read_byte_data(self.address, reg)
    if (self.debug):
      print("I2C: Device 0x%02X returned 0x%02X from reg 0x%02X" % (self.address, result & 0xFF, reg))
    return result
	
  def setPWMFreq(self, freq):
    "Sets the PWM frequency"
    prescaleval = 25000000.0    # 25MHz
    prescaleval /= 4096.0       # 12-bit
    prescaleval /= float(freq)
    prescaleval -= 1.0
    if (self.debug):
      print("Setting PWM frequency to %d Hz" % freq)
      print("Estimated pre-scale: %d" % prescaleval)
    prescale = math.floor(prescaleval + 0.5)
    if (self.debug):
      print("Final pre-scale: %d" % prescale)

    oldmode = self.read(self.__MODE1);
    newmode = (oldmode & 0x7F) | 0x10        # sleep
    self.write(self.__MODE1, newmode)        # go to sleep
    self.write(self.__PRESCALE, int(math.floor(prescale)))
    self.write(self.__MODE1, oldmode)
    time.sleep(0.005)
    self.write(self.__MODE1, oldmode | 0x80)

  def setPWM(self, channel, on, off):
    "Sets a single PWM channel"
    self.write(self.__LED0_ON_L+4*channel, on & 0xFF)
    self.write(self.__LED0_ON_H+4*channel, on >> 8)
    self.write(self.__LED0_OFF_L+4*channel, off & 0xFF)
    self.write(self.__LED0_OFF_H+4*channel, off >> 8)
    if (self.debug):
      print("channel: %d  LED_ON: %d LED_OFF: %d" % (channel,on,off))
	  
  def setServoPulse(self, channel, pulse):
    "Sets the Servo Pulse,The PWM frequency must be 50HZ"
    pulse = pulse*4096/20000        #PWM frequency is 50HZ,the period is 20000us
    self.setPWM(channel, 0, int(pulse))

  def reset(self):
    "reset position"
    time.sleep(1)
    # a droite
    self.setServoPulse(self.ROTATION_SERVO_ID, self.ROTATION_POS_BASE)
    self.ROTATION_POS_CURRENT = self.ROTATION_POS_BASE

    # en haut
    self.setServoPulse(self.LIFT_SERVO_ID, self.LIFT_POS_BASE)
    self.LIFT_POS_CURRENT = self.LIFT_POS_BASE

    # plie
    self.setServoPulse(self.DEPLOY_SERVO_ID, self.DEPLOY_POS_BASE)
    self.DEPLOY_POS_CURRENT = self.DEPLOY_POS_BASE

    # ouvert
    self.setServoPulse(self.PINCH_SERVO_ID, self.PINCH_POS_BASE)
    self.PINCH_POS_CURRENT = self.PINCH_POS_BASE

  def rotate(self, newPos):
    "smooth move position"
    movementStep = 10
    if (self.ROTATION_POS_CURRENT > newPos):
      movementStep = -10

    for i in range(self.ROTATION_POS_CURRENT, newPos, movementStep):  
      pwm.setServoPulse(self.ROTATION_SERVO_ID,i)   
      time.sleep(0.01)
      self.ROTATION_POS_CURRENT = i

    time.sleep(0.5)

  def lift(self, newPos):
    "smooth move position"
    movementStep = 10
    if (self.LIFT_POS_CURRENT > newPos):
      movementStep = -10

    for i in range(self.LIFT_POS_CURRENT, newPos, movementStep):  
      pwm.setServoPulse(self.LIFT_SERVO_ID,i)   
      time.sleep(0.01)
      self.LIFT_POS_CURRENT = i

    time.sleep(0.5)

  def deploy(self, newPos):
    "smooth move position"
    movementStep = 10
    if (self.DEPLOY_POS_CURRENT > newPos):
      movementStep = -10

    for i in range(self.DEPLOY_POS_CURRENT, newPos, movementStep):  
      pwm.setServoPulse(self.DEPLOY_SERVO_ID,i)   
      time.sleep(0.01)
      self.DEPLOY_POS_CURRENT = i

    time.sleep(0.5)

  def pinch(self, newPos):
    "smooth move position"
    movementStep = 10
    if (self.PINCH_POS_CURRENT > newPos):
      movementStep = -10

    for i in range(self.PINCH_POS_CURRENT, newPos, movementStep):  
      pwm.setServoPulse(self.PINCH_SERVO_ID,i)   
      time.sleep(0.01)
      self.PINCH_POS_CURRENT = i

    time.sleep(0.5)

if __name__=='__main__':
 
  pwm = PCA9685(0x40, debug=False)
  pwm.setPWMFreq(50)

# hauteur id:5 / profondeur id:15 90deg
# max 2400
# min 1400

# rotation id:0 / grip id:10 180deg
# max 2500
# min 500
 
#reset
pwm.reset()   
##############

pwm.rotate(2500)
pwm.pinch(2500)
pwm.lift(2000)
pwm.deploy(1900)
pwm.pinch(500)
pwm.deploy(2400)
pwm.lift(1400)

pwm.rotate(500)
pwm.lift(2000)
pwm.deploy(1900)
pwm.pinch(2500)
pwm.deploy(2400)
pwm.lift(1400)
pwm.rotate(1500)

##############
#reset 
pwm.reset()
