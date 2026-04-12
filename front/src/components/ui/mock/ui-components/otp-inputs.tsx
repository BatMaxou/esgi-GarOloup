'use client';

import Typography from '@/components/ui/atoms/typography';
import GlassPanel from '@/components/ui/atoms/glass-panel';
import OTPInput from '@/components/ui/molecules/otp-input';

const OTPInputs = () => {
  return (
    <>
      <Typography tag="h2" variant="heading-2" bold className="mt-8 mb-4 block">
        OTP Input
      </Typography>

      <div className="flex w-full">
        <GlassPanel className="w-full max-w-2xl p-8">
          <ul className="flex w-full flex-col gap-10">
            <li>
              <OTPInput
                length={6}
                label="Par défaut (6 chiffres, taille md)"
                name="otp-default"
                defaultValue=""
                aria-label="OTP par défaut"
              />
            </li>
            <li>
              <OTPInput
                length={6}
                label="État erreur"
                error
                defaultValue="12"
                name="otp-error"
                aria-label="OTP en erreur"
              />
            </li>
            <li>
              <OTPInput
                length={6}
                label="État succès"
                success
                defaultValue="123456"
                name="otp-success"
                aria-label="OTP valide"
              />
            </li>
            <li>
              <OTPInput
                length={6}
                label="Désactivé"
                disabled
                defaultValue="840291"
                name="otp-disabled"
                aria-label="OTP désactivé"
              />
            </li>
            <li>
              <OTPInput
                length={5}
                inputMode="text"
                label="Mode texte (5 caractères)"
                name="otp-text"
                defaultValue="AB"
                aria-label="OTP alphanumérique"
              />
            </li>
          </ul>
        </GlassPanel>
      </div>
    </>
  );
};

export default OTPInputs;
