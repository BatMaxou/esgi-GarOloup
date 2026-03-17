import Typography from '@/components/ui/atoms/typography';
import Button from '@/components/ui/molecules/button';
import GlassPanel from '@/components/ui/atoms/glass-panel';

const Buttons = () => {
  return (
    <>
      <Typography tag="h2" variant="heading-2" bold className="mt-8 mb-4 block">
        Buttons
      </Typography>
      <div className="flex flex-col gap-8">
        <GlassPanel className="p-8">
          <ul className="flex flex-wrap items-center gap-6">
            <li>
              <Button variant="primary" label="Primary" />
            </li>
            <li>
              <Button variant="primary" glass label="Primary Glass" />
            </li>
            <li>
              <Button variant="secondary" label="Secondary" />
            </li>
            <li>
              <Button variant="secondary" glass label="Secondary Glass" />
            </li>
            <li>
              <Button variant="accent" label="Accent" />
            </li>
            <li>
              <Button variant="accent" glass label="Accent Glass" />
            </li>
            <li>
              <Button variant="neutral" label="Neutral" />
            </li>
            <li>
              <Button variant="neutral" glass label="Neutral Glass" />
            </li>
            <li>
              <Button variant="error" label="Error" />
            </li>
            <li>
              <Button variant="error" glass label="Error Glass" />
            </li>
            <li>
              <Button variant="success" label="Success" />
            </li>
            <li>
              <Button variant="success" glass label="Success Glass" />
            </li>
            <li>
              <Button variant="gradient" label="Gradient" />
            </li>
            <li>
              <Button variant="text" label="Text" />
            </li>
          </ul>
        </GlassPanel>
        <GlassPanel className="p-8">
          <ul className="flex flex-wrap items-center gap-6">
            <li>
              <Button variant="gradient" size="lg" label="Large" />
            </li>
            <li>
              <Button variant="gradient" size="md" label="Medium" />
            </li>
            <li>
              <Button variant="gradient" size="sm" label="Small" />
            </li>
          </ul>
        </GlassPanel>
        <GlassPanel className="p-8">
          <ul className="flex flex-wrap items-center gap-6">
            <li>
              <Button variant="gradient" popup label="Popup" />
            </li>
          </ul>
        </GlassPanel>
      </div>
    </>
  );
};

export default Buttons;
